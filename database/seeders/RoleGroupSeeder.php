<?php

namespace Database\Seeders;

use App\Models\RoleGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sites = ['Corporate', 'Plant'];
        
        $processes_roles = [
            'OOS Microbiology' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Effectiveness Check' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Root Cause Analysis' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Change Control' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Lab Incident' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'CAPA' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Audit Program' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Internal Audit' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'External Audit' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Management Review' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Risk Assessment' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Action Item' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Extension' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Observation' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'OOS Chemical' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'OOT' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'OOC' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Deviation' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'New Document' => ['Initiator', 'HOD/Designee', 'QA', 'Approver', 'Reviewer', 'Drafter', 'Trainer', 'View Only', 'FP'],
            'Market Complaint' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Non Conformance' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Incident' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'Failure Investigation' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP'],
            'ERRATA' => ['Initiator', 'HOD/Designee', 'Approver', 'Reviewer', 'Drafter', 'View Only', 'FP']
            // Add other processes and their roles here
        ];
        
        
        $start_from_id = 1; // Initialize your starting ID
        
        foreach ($sites as $site) {
            foreach ($processes_roles as $process => $roles) {
                foreach ($roles as $role) {
                    $group = new RoleGroup();
                    $group->id = $start_from_id;
                    $group->name = "$site-$process-$role";
                    $group->description = "$site-$process-$role";
                    $group->permission = json_encode(['read' => true, 'create' => true, 'edit' => true, 'delete' => true]);
                    $group->save();
        
                    $start_from_id++;
                }
            }
        }


        // For seeding cft roles.
        
        $cft_roles = [
            "Production",
            "Warehouse",
            "Quality Control",
            "Quality Assurance",
            "Engineering",
            "Analytical Development Laboratory",
            "Process Development Laboratory / Kilo Lab",
            "Technology Transfer / Design",
            "Environment, Health & Safety",
            "Human Resource & Administration",
            "Information Technology",
            "Project Management"
        ];
        
        $processes = [
            'Deviation',
            'Change Control',
            'Non Conformance',
            'Incident',
            'Failure Investigation'
        ];
        
        $incrementCount = $start_from_id;
        
        foreach ($processes as $process) {
            foreach ($sites as $site) {
                foreach ($cft_roles as $role) {
                    $group = new RoleGroup();
                    $group->id = $incrementCount++;
                    $group->name = "$site-$process-$role";
                    $group->description = "$site-$process-$role";
                    $group->permission = json_encode(['read' => true, 'create' => true, 'edit' => true, 'delete' => true]);
                    $group->save();
                }
            }
        }

    }
}
