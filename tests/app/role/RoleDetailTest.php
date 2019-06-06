<?php
namespace UnitTest\App\Role;

use Tests\CommonTestCase;

class RoleDetailTest extends CommonTestCase
{

    // 角色详情测试
    public function testRoleDetail()
    {
        $response = $this->json('GET', '/admin/roles/2', [], $this->header);
        $result = json_decode($response->response->content(), true);


        $this->assertEquals(0, $result['errorCode']);
        $this->assertEquals('测试角色', $result['data']['name']);
        $this->assertEquals([1], $result['data']['actions']);

    }

    public function testUnknownRole()
    {
        $response = $this->json('GET', '/admin/roles/4', [], $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(UNKNOWN_ROLE, $result['errorCode']);
    }


    /**
     * 预加载数据
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet($this->dataSet('role/role-detail.yaml'));
    }

}
