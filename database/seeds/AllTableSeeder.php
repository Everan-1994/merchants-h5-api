<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AllTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $createdAt = Carbon::now()->toDateTimeString();
        $updatedAt = Carbon::now()->toDateTimeString();

        // adminUsers
        $user = [
            'username' => 'admin',
            'realname' => 'admin',
            'email' => 'admin@douanquan.com',
            'password' => app('hash')->make('admin'),
            'isEnable' => 1,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];

        \DB::table('admin_users')->insert($user);

        // adminRoles
        $role = [
            'name' => '超级管理员',
            'isSuper' => 1,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];

        \DB::table('admin_roles')->insert($role);

        // adminRoleUsers
        $adminRole = [
            'adminRoleId' => 1,
            'adminUserId' => 1,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];

        \DB::table('admin_role_users')->insert($adminRole);

        // actions
        $actions = [
            [
                'id' => 24,
                'parentId' => 0,
                'name' => '权限',
                'route' => '',
                'description' => '仅开发人员使用',
                'sort' => 6,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 25,
                'parentId' => 24,
                'name' => '创建权限',
                'route' => 'Post:/admin/actions',
                'description' => '',
                'sort' => 2,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 26,
                'parentId' => 24,
                'name' => '删除权限',
                'route' => 'Delete:/admin/actions',
                'description' => '',
                'sort' => 4,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 27,
                'parentId' => 0,
                'name' => '权限通过角色进行分配',
                'route' => '',
                'description' => '',
                'sort' => 8,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 28,
                'parentId' => 27,
                'name' => '查看角色列表',
                'route' => 'Get:/admin/roles',
                'description' => '',
                'sort' => 1,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 29,
                'parentId' => 27,
                'name' => '查看角色详情',
                'route' => 'Get:/admin/roles/{id:[0-9]+}',
                'description' => '',
                'sort' => 2,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 30,
                'parentId' => 27,
                'name' => '创建角色',
                'route' => 'Post:/admin/roles',
                'description' => '',
                'sort' => 3,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 31,
                'parentId' => 27,
                'name' => '更新角色',
                'route' => 'Put:/admin/roles',
                'description' => '',
                'sort' => 4,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 32,
                'parentId' => 27,
                'name' => '删除角色',
                'route' => 'Delete:/admin/roles',
                'description' => '',
                'sort' => 5,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 33,
                'parentId' => 0,
                'name' => '成员',
                'route' => '',
                'description' => '后台管理员',
                'sort' => 7,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 34,
                'parentId' => 33,
                'name' => '查看成员列表',
                'route' => 'Get:/admin/members',
                'description' => '',
                'sort' => 1,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 35,
                'parentId' => 33,
                'name' => '查看成员详情',
                'route' => 'Get:/admin/members/{id}',
                'description' => '',
                'sort' => 2,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 36,
                'parentId' => 33,
                'name' => '创建成员',
                'route' => 'Post:/admin/members',
                'description' => '',
                'sort' => 3,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 37,
                'parentId' => 33,
                'name' => '更新成员',
                'route' => 'Put:/admin/members',
                'description' => '',
                'sort' => 4,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 38,
                'parentId' => 33,
                'name' => '删除成员',
                'route' => 'Delete:/admin/members',
                'description' => '',
                'sort' => 5,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 39,
                'parentId' => 33,
                'name' => '禁用、启用成员账户',
                'route' => 'Patch:/admin/members',
                'description' => '',
                'sort' => 6,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
            [
                'id' => 60,
                'parentId' => 24,
                'name' => '更新权限',
                'route' => 'Put:/admin/actions',
                'description' => '',
                'sort' => 3,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ],
        ];

        \DB::table('actions')->insert($actions);
    }
}
