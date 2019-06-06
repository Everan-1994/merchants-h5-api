<?php

namespace UnitTest\App\Role;

use Tests\CommonTestCase;

class RoleAddTest extends CommonTestCase
{
    // 添加角色测试
    public function testRoleAdd()
    {
        $params = [
            'name' => '测试角色',
            'actions' => [
                1,
                2,
                3,
            ],
        ];
        $response = $this->json('POST', '/admin/roles', $params, $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals('success', $result['message']);

        $response = $this->json('GET', '/admin/roles', [], $this->header);
        $result = json_decode($response->response->content(), true);

        $this->assertEquals(4, count($result['data']));
        $this->initSystemDataSet();
    }

    /**
     * 预加载数据.
     *
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet(array_merge(
            $this->dataSet('role/role-list.yaml'),
            [
                'admin_role_actions' => [],
            ]
        ));
    }
}
