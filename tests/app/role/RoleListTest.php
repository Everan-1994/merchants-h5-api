<?php
namespace Test\App\Role;

use Tests\CommonTestCase;

class RoleListTest extends CommonTestCase
{

    // 角色列表测试
    public function testRoleList()
    {
        $response = $this->json('GET', '/admin/roles', [], $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(3, count($result['data']));
        $this->assertEquals('测试角色2', $result['data'][1]['name']);
    }

    /**
     * 预加载数据
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet($this->dataSet('role/role-list.yaml'));
    }

}
