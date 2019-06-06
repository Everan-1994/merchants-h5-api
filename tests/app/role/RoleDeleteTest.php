<?php

namespace Test\App\Role;

use Tests\CommonTestCase;

class RoleDeleteTest extends CommonTestCase
{
    // 删除单个角色测试
    public function testRoleDelete()
    {
        $params = [
            'ids' => [3],
        ];
        $response = $this->json('DELETE', '/admin/roles', $params, $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(0, $result['errorCode']);

        $response = $this->json('GET', '/admin/roles', [], $this->header);
        $result = json_decode($response->response->content(), true);

        $this->assertEquals(2, count($result['data']));
        $this->initSystemDataSet();
    }

    // 删除多个角色测试
    public function testRoleDeleteMany()
    {
        $params = [
            'ids' => [2, 3],
        ];
        $response = $this->json('DELETE', '/admin/roles', $params, $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(0, $result['errorCode']);

        $response = $this->json('GET', '/admin/roles', [], $this->header);
        $result = json_decode($response->response->content(), true);

        $this->assertEquals(1, count($result['data']));
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
            $this->dataSet('role/role-list.yaml')
        ));
    }
}
