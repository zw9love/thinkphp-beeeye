<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31
 * Time: 20:52
 */
//namespace app\host\controller;
require(__DIR__ . "/../../util/Tool.php");
class Host
{

    public $sqlData = null;

    public function __construct()
    {
        $this->sqlData = Tool::getSqlInfo();
    }

    function test(){
        return 'aaa';
    }

    function get($ids)
    {
        $sqlInfo = $this->sqlData;
        $data = Tool::getRequestData();
        try {
//        echo md5($data["login_pwd"]);
            $pdo = new PDO("mysql:host=" . $sqlInfo["serverName"] . ";dbname=beeeyehced", $sqlInfo["username"], $sqlInfo["password"]);
            //ids不存在查询
            if (is_null($ids)) {
                $pageNumber = 0;
                $pageSize = 30;
                //  && count($page) > 0
                if (!is_null($data["page"])) {
                    $page = $data["page"];
                    $pageNumber = $page['pageNumber'] - 1;
                    $pageSize = $page["pageSize"];
                }
                $sql = "select * from beeeye_host limit $pageNumber, $pageSize";
                $countSql = "select count(*) as total from beeeye_host";
                $stmt = $pdo->prepare($sql);
                $stmtCount = $pdo->prepare($countSql);
//        $stmt->bindValue(1, $page["pageNumber"] - 1);
//        $stmt->bindValue(2, $page["pageSize"]);
                $stmt->execute();
                $stmtCount->execute();
//        $res = $stmt->fetch();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $list = array();
                $total = (int)($stmtCount->fetch(PDO::FETCH_ASSOC)["total"]);
                $i = 0;
                foreach ($res as $row) {
                    $i++;
                    array_push($list, $row);
//            echo $row['username'] . '<br/>';
                }
                $postData = array(
                    "pageNumber" => $pageNumber,
                    "pageSize" => $pageSize,
                    "list" => $list,
                    "totalRow" => $total
                );
                echo Tool::getJsonData(200, "成功", $postData);
            } else {
                $sql = "select * from beeeye_host where host_ids = ? ";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(1, $ids);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $row_count = $stmt->rowCount();
                if ($row_count > 0) {
                    echo Tool::getJsonData(200, "成功", $res);
                } else {
                    echo Tool::getJsonData(606, "失败，没有该条记录", null);
                }
            }
            // 关闭连接
            $pdo = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function post()
    {
        $sqlInfo = $this->sqlData;
        $data = Tool::getRequestData();
        try {
            $pdo = new PDO("mysql:host=" . $sqlInfo["serverName"] . ";dbname=beeeyehced", $sqlInfo["username"], $sqlInfo["password"]);
            $sql = "INSERT INTO beeeye_host (host_ids, name, ip, port, os_type, os_version, os_arch, login_name, login_pwd, status) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $hostIds = Tool::getRandomString();
            $stmt->bindValue(1, $hostIds);
            $stmt->bindValue(2, $data["name"]);
            $stmt->bindValue(3, $data["ip"]);
            $stmt->bindValue(4, $data["port"]);
            $stmt->bindValue(5, $data["os_type"]);
            $stmt->bindValue(6, $data["os_version"]);
            $stmt->bindValue(7, $data["os_arch"]);
            $stmt->bindValue(8, $data["login_name"]);
            $stmt->bindValue(9, $data["login_pwd"]);
            $stmt->bindValue(10, 0);
            $execRet = $stmt->execute();
            $row_count = $stmt->rowCount();
            $pdo = null;
            if ($row_count > 0) {
                echo Tool::getJsonData(200, "成功", null);
            } else {
                echo Tool::getJsonData(606, "失败", null);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function put()
    {
        $sqlInfo = $this->sqlData;
        $data = Tool::getRequestData();
        try {
            $pdo = new PDO("mysql:host=" . $sqlInfo["serverName"] . ";dbname=beeeyehced", $sqlInfo["username"], $sqlInfo["password"]);
            $sql = "UPDATE beeeye_host SET name = ?, ip = ?, port = ?,login_name = ?,login_pwd = ? where host_ids = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $data["name"]);
            $stmt->bindValue(2, $data["ip"]);
            $stmt->bindValue(3, $data["port"]);
            $stmt->bindValue(4, $data["login_name"]);
            $stmt->bindValue(5, $data["login_pwd"]);
            $stmt->bindValue(6, $data["host_ids"]);
            $execRet = $stmt->execute();
            $row_count = $stmt->rowCount();
            $pdo = null;
            if ($row_count > 0) {
                echo Tool::getJsonData(200, "成功", null);
            } else {
                echo Tool::getJsonData(606, "失败", null);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function delete()
    {
        $sqlInfo = $this->sqlData;
        $data = Tool::getRequestData();
        try {
            $pdo = new PDO("mysql:host=" . $sqlInfo["serverName"] . ";dbname=beeeyehced", $sqlInfo["username"], $sqlInfo["password"]);
            $sql = "DELETE FROM beeeye_host where host_ids = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $data["host_ids"]);
            $execRet = $stmt->execute();
            $row_count = $stmt->rowCount();
            $pdo = null;
            if ($row_count > 0) {
                echo Tool::getJsonData(200, "成功", null);
            } else {
                echo Tool::getJsonData(606, "失败", null);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}



