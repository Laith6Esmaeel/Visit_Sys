<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. المدير العام (كل الصلاحيات)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. الريسبشن (الزوار + سجل الزيارات بكامل العمليات)
        $receptionist = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionistPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%_visitor')
                ->orWhere('name', 'like', '%_visit::record');
        })->get();
        $receptionist->syncPermissions($receptionistPermissions);

        // 3. مدير المديرية (المديريات + الدوائر + الغرف + الموظفين بكامل العمليات)
        $directorateManager = Role::firstOrCreate(['name' => 'directorate_manager']);
        $managerPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%_directorate')
                ->orWhere('name', 'like', '%_department')
                ->orWhere('name', 'like', '%_room')
                ->orWhere('name', 'like', '%_employee');
        })->get();
        $directorateManager->syncPermissions($managerPermissions);
    }
}
