<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserImportController extends Controller
{
    /**
     * Show the import form
     */
    public function showImportForm()
    {
        return view('admin.users.import');
    }

    /**
     * Process the imported file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // Max 5MB
        ]);

        $file = $request->file('file');
        $results = $this->processFile($file);

        return view('admin.users.import', [
            'results' => $results,
            'showResults' => true,
        ]);
    }

    /**
     * Process the CSV file and return results
     */
    private function processFile($file)
    {
        $handle = fopen($file->getPathname(), 'r');
        
        // Read header row
        $header = fgetcsv($handle);
        
        // Normalize header (trim whitespace, lowercase)
        $header = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

        // Expected columns (id_number is optional)
        $expectedColumns = ['name', 'email', 'id_number', 'role', 'password', 'confirm_password'];
        
        // Map column names to indices
        $columnMap = [];
        foreach ($expectedColumns as $col) {
            $index = array_search($col, $header);
            if ($index === false) {
                // Try alternative names
                if ($col === 'confirm_password') {
                    $index = array_search('password_confirmation', $header);
                }
            }
            $columnMap[$col] = $index;
        }

        $results = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'created_users' => [],
        ];

        $rowNumber = 1; // Start after header
        $existingEmails = User::pluck('email')->map(fn($e) => strtolower($e))->toArray();
        $importedEmails = []; // Track emails being imported in this batch

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $results['total']++;

            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }

            // Extract data
            $data = [
                'name' => $columnMap['name'] !== false ? trim($row[$columnMap['name']] ?? '') : '',
                'email' => $columnMap['email'] !== false ? strtolower(trim($row[$columnMap['email']] ?? '')) : '',
                'id_number' => $columnMap['id_number'] !== false ? trim($row[$columnMap['id_number']] ?? '') : '',
                'role' => $columnMap['role'] !== false ? strtolower(trim($row[$columnMap['role']] ?? '')) : '',
                'password' => $columnMap['password'] !== false ? ($row[$columnMap['password']] ?? '') : '',
                'confirm_password' => $columnMap['confirm_password'] !== false ? ($row[$columnMap['confirm_password']] ?? '') : '',
            ];

            // Validate row
            $errors = $this->validateRow($data, $existingEmails, $importedEmails);

            if (!empty($errors)) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'data' => $data,
                    'messages' => $errors,
                ];
                continue;
            }

            try {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'id_number' => $data['id_number'] ?: null,
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                    'email_verified_at' => now(),
                ]);

                $results['success']++;
                $results['created_users'][] = [
                    'row' => $rowNumber,
                    'name' => $user->name,
                    'email' => $user->email,
                    'id_number' => $user->id_number,
                    'role' => $user->role,
                ];

                // Add to tracking arrays
                $existingEmails[] = $data['email'];
                $importedEmails[] = $data['email'];

                ActivityLog::log('Imported user: ' . $user->email, auth()->id(), [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'import_row' => $rowNumber,
                ]);
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'data' => $data,
                    'messages' => ['Database error: ' . $e->getMessage()],
                ];
            }
        }

        fclose($handle);

        return $results;
    }

    /**
     * Validate a single row
     */
    private function validateRow(array $data, array $existingEmails, array $importedEmails): array
    {
        $errors = [];

        // Required fields
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email format is invalid';
        } elseif (in_array($data['email'], $existingEmails)) {
            $errors[] = 'Email already exists in database';
        } elseif (in_array($data['email'], $importedEmails)) {
            $errors[] = 'Email is duplicated in this import file';
        }

        if (empty($data['role'])) {
            $errors[] = 'Role is required';
        } elseif (!in_array($data['role'], ['admin', 'mentor', 'participant'])) {
            $errors[] = 'Role must be: admin, mentor, or participant';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Password and confirm_password do not match';
        }

        return $errors;
    }

    /**
     * Download template CSV file
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_import_template.csv"',
        ];

        $columns = ['name', 'email', 'id_number', 'role', 'password', 'confirm_password'];
        $exampleRow = ['John Doe', 'john@example.com', '2024001234', 'participant', 'password123', 'password123'];

        $callback = function() use ($columns, $exampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $exampleRow);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
