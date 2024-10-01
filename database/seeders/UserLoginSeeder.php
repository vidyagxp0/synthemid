<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Roles;
use App\Models\QMSRoles;
use App\Models\QMSProcess;
use App\Models\QMSDivision;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class UserLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleGroupCount = Roles::count();
        $users = [
            // [
            //     'name' => 'Umesh Kulkarni',
            //     'email' => 'qa05@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Sandip Patil',
            //     'email' => 'qc02@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Somnath Shinde',
            //     'email' => 'somnath@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Nandlal Gupta',
            //     'email' => 'cqa2@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Mahadev Patil',
            //     'email' => 'engineering@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Sachin Bhasme',
            //     'email' => 'injection_pune@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Tirupati Survase',
            //     'email' => 'tab-qms@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Sanjay / Rahul',
            //     'email' => 'edp.agiopune@gmail.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Anil Bade',
            //     'email' => 'stores_pune@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Sanjay Dhumal',
            //     'email' => 'sanjayd@agio-pharma.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            // [
            //     'name' => 'Satyam Dabekar',
            //     'email' => 'satyam.agio@gmail.com',
            //     'password' => Hash::make(1),
            //     'departmentid' => 1,
            //     'roles' => range(1, $roleGroupCount),
            // ],
            [
                'name' => 'Himanshu Patil',
                'email' => 'himanshupatil5690@gmail.com',
                'password' => '$2y$10$ybcHMuQ8soPzXcdljEQ/wOUx0JximT3yb5naubluqz3TjOz/tGBlC',
                'departmentid' => 1,
                'roles' => range(1, $roleGroupCount),
            ],
            [
                'name' => 'Vikash Prajapati',
                'email' => 'vikash@gmail.com',
                'password' => '$2y$10$ybcHMuQ8soPzXcdljEQ/wOUx0JximT3yb5naubluqz3TjOz/tGBlC',
                'departmentid' => 1,
                'roles' => range(1, $roleGroupCount),
            ],
            [
                'name' => 'Gautam Solanki',
                'email' => 'gautamsolankigxp@gmail.com',
                'password' => '$2y$10$ybcHMuQ8soPzXcdljEQ/wOUx0JximT3yb5naubluqz3TjOz/tGBlC',
                'departmentid' => 1,
                'roles' => range(1, $roleGroupCount),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => '$2y$10$ybcHMuQ8soPzXcdljEQ/wOUx0JximT3yb5naubluqz3TjOz/tGBlC',
                'departmentid' => $userData['departmentid'],
            ]);

            $usertableRole = ''; // Initialize the variable to store concatenated role IDs

            foreach ($userData['roles'] as $roleId) {
                $checkRole = Roles::find($roleId);

                if ($checkRole === null) {
                    // Log or handle the missing role
                    // echo "Role ID {$roleId} does not exist.\n";
                    continue;
                }

                // Split the string using the '-' delimiter
                $roleArray = explode('-', $checkRole->name);

                // Assign values to three variables
                $q_m_s_divisions_name = trim($roleArray[0]);
                $q_m_s_processes_name = trim($roleArray[1]);
                $q_m_s_roles_name = trim($roleArray[2]);

                // Assuming you have models for q_m_s_divisions and q_m_s_process
                $division = QMSDivision::where('name', $q_m_s_divisions_name)->first();
                $processes = QMSProcess::where('process_name', $q_m_s_processes_name)->get();
                $qmsroles = QMSRoles::where('name', $q_m_s_roles_name)->first();
                
                foreach ($processes as $process) {
                    $q_m_s_divisions_id = $division->id;
                    $q_m_s_processes_id = $process->id;
                    if($qmsroles == null){
                        dd($q_m_s_roles_name);
                    }
                    $q_m_s_roles_id = $qmsroles->id;
                    
                    $userRole = new UserRole();                
                    // Concatenate the q_m_s_roles_id with previous ones
                    $usertableRole .= $q_m_s_roles_id . ',';

                    $userRole->user_id = $user->id;
                    $userRole->role_id = $roleId;
                    $userRole->q_m_s_divisions_id = $q_m_s_divisions_id;
                    $userRole->q_m_s_processes_id = $q_m_s_processes_id;
                    $userRole->q_m_s_roles_id = $q_m_s_roles_id;
                    $userRole->save();
                }
            }

            // Remove the trailing comma from the concatenated string
            $usertableRole = rtrim($usertableRole, ',');

            // Explode the concatenated string into an array
            $rolesArray = explode(',', $usertableRole);

            // Remove duplicate entries
            $uniqueRolesArray = array_unique($rolesArray);

            // Implode the unique array back into a string
            $uniqueUsertableRole = implode(',', $uniqueRolesArray);

            // Update the user table with the unique concatenated role IDs
            $user->role = $uniqueUsertableRole;
            $user->save();
        }
    }
}
