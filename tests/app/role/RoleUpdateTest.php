<?php

namespace UnitTest\App\Role;

use Tests\CommonTestCase;

class RoleUpdateTest extends CommonTestCase
{
    // 更新角色测试
    public function testRoleUpdate()
    {
        $params = [
            'id' => 2,
            'name' => '测试角色Update',
            'actions' => [2, 3],
        ];
        $response = $this->json('PUT', '/admin/roles', $params, $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(0, $result['errorCode']);

        // 查询是否一致
        $detailResponse = $this->json('GET', '/admin/roles/'.$params['id'], [], $this->header);
        $detailResult = json_decode($detailResponse->response->content(), true);
        $this->assertEquals($params['actions'], $detailResult['data']['actions']);
        $this->initSystemDataSet();
    }

    /**
     * 预加载数据.
     *
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet($this->dataSet('role/role-detail.yaml'));
    }
}
