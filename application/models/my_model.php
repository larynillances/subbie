<?php
class my_model extends CI_Model{
    var $model_config;
    var $model_setting;

    function __construct(){
        parent::__construct();
        $this->resetValue();
    }

    function setSetting($setting){
        $this->model_setting = array_merge($this->model_setting, $setting);
    }

    function setConfigValue($config){
        $this->model_config = array_merge($this->model_config, $config);
    }

    function resetValue(){
        $this->model_config = array(
            'isSearch' => false,
            'isTest' => false,
            'isProtectIdentifiers' => true,
            'is_enable' => false,
            'start' => 0,
            'per_page' => 10,
            'whatNum' => array('id'),
            'hasOrder' => false,
            'theOrder' => array(
                'what' => 'id',
                'order' => 'DESC'
            ),
            'hasGroupBy' => false,
            'groupBy' => '',
            'isRandomized' => false,
            'limitRandomTo' => 1,
            "isDistinct" => false,
            'selectFields' => "",
            'escapeSelect' => true,
            'isNoOr' => false,
            'isLastInsert' => false,
            'whatLastInsert' => 'id',
            'isShift' => false,
            'isNormalized' => false,
            'normalizedFields' => "",
            'normalizedTo' => "",
            'normalizedToArray' => false,
            'connectorArray' => array(),
            'hasJoin' => false,
            'theJoin' => array(
                'table' => '',
                'join_field' => 'id',
                'source_field' => 'id',
                'join_append' => '',
                'type' => ''
            ),
            'isCount' => false,
            'noReset' => false,
            'isReturnObject' => false,
            'isReturnArray' => false,
            'returnWithIncrement' => array(
                'isReturn' => false,
                'fieldName' => 'ref',
                'fieldStart' => 1
            )
        );

        $this->model_setting = array(
            'username' => "root",
            'password' => "",
            'db' => ""
        );
    }

    function newDbCon($hostname = "localhost", $showError = TRUE){
        $config['hostname'] = $hostname;
        $config['username'] = $this->model_setting['username'];
        $config['password'] = $this->model_setting['password'];
        $config['database'] = $this->model_setting['db'];
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = $showError;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";

        $ps = $this->load->database($config, TRUE);
        return $ps;
    }

    function dataCleaner($data, $toClearData = true){
        $parentType = "";
        if(is_array($data) || is_object($data)){
            if(is_object($data)){
                $parentType = 'obj';
            }
            if(is_array($data)){
                $parentType = 'array';
            }

            if(count($data)>0){
                foreach($data as $k=>$v){
                    $type = "";
                    if(is_object($v)){
                        $type = 'obj';
                    }
                    if(is_array($v)){
                        $type = 'array';
                    }

                    switch($type){
                        case "obj":
                            $data->$k = $this->dataCleaner($v, $toClearData);
                            break;
                        case "array":
                            $data[$k] = $this->dataCleaner($v, $toClearData);
                            break;
                        default:
                            $thisVal = $this->security->xss_clean($v);
                            switch($parentType){
                                case "obj":
                                    $data->$k = $toClearData ? $this->stripHTMLTags($thisVal) : $thisVal;
                                    break;
                                case "array":
                                    $data[$k] = $toClearData ? $this->stripHTMLTags($thisVal) : $thisVal;
                                    break;
                                default:
                                    $data[$k] = $toClearData ? $this->stripHTMLTags($thisVal) : $thisVal;
                                    break;
                            }
                            break;
                    }
                }
            }
        }else{
            $data = addslashes($data);
            $thisVal = $this->security->xss_clean($data);
            $data = $toClearData ? $this->stripHTMLTags($thisVal) : $thisVal;
        }

        return $data;
    }

    function arrayWalk($array, $append, $type = 'front', $as = ''){
        $ar = array();
        if(count($array)>0){
            foreach($array as $k=>$v){
                switch($type){
                    case 'back':
                        $ar[$k] = $v . $append;
                        break;
                    case 'as':
                        $ar[$k] = $append . $v . ' as ' . $as . $v;
                        break;
                    default:
                        $ar[$k] = $append . $v;
                }
            }
        }

        return $ar;
    }

    function stripHTMLTags($str){
        $t = preg_replace('/<[^<|>]+?>/', '', htmlspecialchars_decode($str));
        $t = htmlentities($t, ENT_QUOTES, "UTF-8");
        return $t;
    }

    function lastId($db, $para = ''){
        if(!$para){
            $para = $this->db;
        }

        $id = "";
        $q = $para->get($db);
        if($q->num_rows() > 0){
            foreach ($q->result() as $row){
                $id = $row->id;
            }
        }

        return $id;
    }

    function insert($db, $data, $toClearData = true, $para = ''){
        $data = $this->dataCleaner($data, $toClearData);

        if(!$para){
            $para = $this->db;
        }
        $para->insert($db,$data);

        return $para->insert_id();
    }

    function insertBatch($db, $data, $toClearData = true, $para = ''){
        $data = $this->dataCleaner($data, $toClearData);

        if(!$para){
            $para = $this->db;
        }
        $para->insert_batch($db, $data);
    }

    function update($db, $data, $id, $what = 'id', $toClearData = true, $para = ''){
        if(!$para){
            $para = $this->db;
        }

        $this->whereOptions($id, $what, $para);
        $data = $this->dataCleaner($data, $toClearData);
        $para->update($db, $data);

        return $para->insert_id();
    }

    function updateBatch($db, $data, $id, $what = 'id', $toClearData = true, $para = ''){
        if(!$para){
            $para = $this->db;
        }

        $this->whereOptions($id, $what, $para);
        $data = $this->dataCleaner($data, $toClearData);
        $para->update_batch($db, $data);
    }

    function delete($db,$id,$what = 'id', $para = ''){
        if(!$para){
            $para = $this->db;
        }

        $this->whereOptions($id, $what, $para);
        $para->delete($db);
    }

    function getInfo($db,$id = '',$what = 'id', $para = ''){
        if(!$para){
            $para = $this->db;
        }
        $para->query('SET SQL_BIG_SELECTS=1');

        $order = $this->model_config['theOrder']['order'];
        $whatOrder = $this->model_config['hasOrder'] ? $this->model_config['theOrder']['what'] : '';

        //to set _protect_identifiers to false and skips escaping
        if(!$this->model_config['isProtectIdentifiers']){
            $para->_protect_identifiers = FALSE;
        }

        if($whatOrder){
            $whatNum = $this->model_config['whatNum'];
            if(is_array($whatOrder)){
                if(count($whatOrder)>0){
                    foreach($whatOrder as $k=>$v){
                        $this_order = is_array($order) ? $order[$k] : $order;
                        if(in_array($v, $whatNum)){
                            $para->order_by('CAST('.$v.' AS UNSIGNED INTEGER)', $this_order);
                        }else{
                            if($this_order){
                                $para->order_by($v, $this_order);
                            }else{
                                $para->order_by($v);
                            }
                        }
                    }
                }
            }else{
                if(in_array($whatOrder, $whatNum)){
                    $para->order_by('CAST('.$whatOrder.' AS UNSIGNED INTEGER)', $order);
                }else{
                    if($order){
                        $para->order_by($whatOrder, $order);
                    }else{
                        $para->order_by($whatOrder);
                    }
                }
            }
        }

        //set _protect_identifiers back to TRUE or ALL of your queries from now on will be non-escaped
        if(!$this->model_config['isProtectIdentifiers']){
            $para->_protect_identifiers = TRUE;
        }

        if($id){
            if($what == ''){
                $para->where($id);
            }else{
                $this->whereOptions($id, $what, $para);
            }
        }

        if($this->model_config['is_enable'] == true){
            $para->limit($this->model_config['per_page'], $this->model_config['start']);
        }

        if($this->model_config['selectFields']){
            $para->select($this->model_config['selectFields'], $this->model_config['escapeSelect']);
        }
        if($this->model_config['isDistinct']){
            $para->distinct();
        }

        if($this->model_config['hasGroupBy']){
            $para->group_by($this->model_config['groupBy']);
        }

        $para->from($db);
        if($this->model_config['hasJoin'] && $this->model_config['theJoin']['table']){
            if(is_array($this->model_config['theJoin']['table'])){
                foreach($this->model_config['theJoin']['table'] as $k=>$table_name){
                    $join_field = is_array($this->model_config['theJoin']['join_field']) ? $this->model_config['theJoin']['join_field'][$k] : $this->model_config['theJoin']['join_field'];
                    $source_field = is_array($this->model_config['theJoin']['source_field']) ? $this->model_config['theJoin']['source_field'][$k] : $this->model_config['theJoin']['source_field'];
                    $type = is_array($this->model_config['theJoin']['type']) ? $this->model_config['theJoin']['type'][$k] : $this->model_config['theJoin']['type'];

                    $tn = $table_name;
                    if(is_array($this->model_config['theJoin']['join_append'])){
                        $tn = $this->model_config['theJoin']['join_append'][$k] ? $this->model_config['theJoin']['join_append'][$k] : $table_name;
                    }else{
                        $tn = $this->model_config['theJoin']['join_append'] ? $this->model_config['theJoin']['join_append'] : $table_name;
                    }

                    $join_query = "";
                    if(is_array($join_field)){

                        $ref = 0;
                        foreach($join_field as $new_join_k=>$new_join_field){
                            $join_query .= $ref>0 ? " AND " : "";
                            $join_query .= $tn . "." . $new_join_field . " = ";
                            $join_query .= is_array($source_field) ? $source_field[$new_join_k] : $source_field;

                            $ref++;
                        }
                    }else{
                        $join_query = $tn . "." . $join_field . " = " . $source_field;
                    }

                    $para->join($table_name, $join_query, $type);
                }
            }else{
                $tn = $this->model_config['theJoin']['join_append'] ? $this->model_config['theJoin']['join_append'] : $this->model_config['theJoin']['table'];
                $join_query = $tn . "." . $this->model_config['theJoin']['join_field'] . " = " . $db . "." . $this->model_config['theJoin']['source_field'];
                $para->join($this->model_config['theJoin']['table'], $join_query, $this->model_config['theJoin']['type']);
            }
        }
        $query = $para->get();

        $data = $this->model_config['isLastInsert'] ? "" : array();
        if($this->model_config['isCount']){
            $data = $para->count_all_results($db);
        }else{
            $data = $this->queryData($query);
        }

        return $data;
    }

    function mysqlString($sql, $hasNoReturn = false){
        $query = $this->db->query($sql);

        if(!$hasNoReturn){
            return $this->queryData($query);
        }
    }

    function queryData($query){
        $data = $this->model_config['isLastInsert'] ? "" : array();

        if($query->num_rows() > 0){
            if($this->model_config['isLastInsert']){
                $last = $query->last_row('array');

                if($this->model_config['whatLastInsert']){
                    if(is_array($this->model_config['whatLastInsert'])){
                        $temp = array();
                        foreach($this->model_config['whatLastInsert'] as $k=>$v){
                            $temp[$v] = $last[$v];
                        }
                        $data = $temp;
                    }else{
                        $data = $last[$this->model_config['whatLastInsert']];
                    }
                }else{
                    $data = $last;
                }
            }else if($this->model_config['isShift']){
                $data = array_shift($query->result_array());
            }else{
                $ref = (int)$this->model_config['returnWithIncrement']['fieldStart'];
                foreach ($query->result() as $row){
                    if($this->model_config['isNormalized'] && $this->model_config['selectFields']){
                        $notArray = true;
                        $thisField = $this->model_config['normalizedFields'] ? $this->model_config['normalizedFields'] : $this->model_config['selectFields'];
                        $thisTo = $this->model_config['normalizedTo'] ? $this->model_config['normalizedTo'] : '';

                        if(is_array($thisField)){
                            if(count($thisField)>0){
                                foreach($thisField as $key=>$field_name){
                                    if($thisTo){
                                        $thisRefToUse = is_array($thisTo) ? $thisTo[$key] : $thisTo;
                                        if($this->model_config['normalizedToArray']){
                                            $data[$field_name][$row->$thisRefToUse][] = $row->$thisField;
                                        }
                                        else{
                                            $data[$field_name][$row->$thisRefToUse] = $row->$thisField;
                                        }
                                    }
                                    else{
                                        $data[$field_name][] = $row->$field_name;
                                    }
                                }

                                $notArray = false;
                            }
                        }

                        if($notArray){
                            $thisField = (String)$thisField;
                            if($thisTo){
                                if($this->model_config['normalizedToArray']){
                                    $data[$row->$thisTo][] = $row->$thisField;
                                }
                                else{
                                    $data[$row->$thisTo] = $row->$thisField;
                                }
                            }
                            else{
                                $data[] = $row->$thisField;
                            }
                        }
                    }
                    else{
                        if($this->model_config['returnWithIncrement']['isReturn']){
                            $incrementFieldName = $this->model_config['returnWithIncrement']['fieldName'];
                            $row->$incrementFieldName = $ref;
                        }

                        $data[] = $this->model_config['isReturnArray'] ? (array)$row : $row;
                    }

                    $ref++;
                }
            }

            $dataCount = count($data);
            if($dataCount>0){
                if($this->model_config['isRandomized'] && $dataCount > $this->model_config['limitRandomTo']){
                    while(count($data) != $this->model_config['limitRandomTo']){
                        $randId = rand(0, $dataCount);
                        unset($data[$randId]);
                        sort($data);
                    }
                }
            }
        }

        if($this->model_config['isReturnObject'] && is_array($data)){
            $data = (Object)$data;
        }

        if($this->model_config['noReset'] == false){
            $this->resetValue();
        }

        return $data;
    }

    function getFields($db, $except = array(), $para = ''){
        if(!$para){
            $para = $this->db;
        }

        $fields = $para->list_fields($db);
        if(count($fields)>0){
            foreach($fields as $k=>$v){
                if(count($except)>0){
                    if(in_array($v, $except)){
                        unset($fields[$k]);
                    }
                }
            }
        }

        return $fields;
    }

    //region WHERE OPTIONS
    function whereOptions($id, $what, $para){
        if(is_array($id) && is_array($what)){
            if(count($what)>0){
                foreach($what as $k=>$v){
                    $isOk = false;
                    if($k!==0 && $this->model_config['isSearch']){
                        $isOk = true;
                    }

                    if(count($this->model_config['connectorArray'])>0){
                        $isOk = array_key_exists($k, $this->model_config['connectorArray']) ? $this->model_config['connectorArray'][$k] : $isOk;
                    }

                    $this->whereFunc($v,$id[$k],$para,$isOk);
                }
            }
        }else{
            if(is_array($what)){
                if(count($what)>0){
                    foreach($what as $k=>$v){
                        $isOk = false;
                        if($k!==0 && !$this->model_config['isNoOr']){
                            $isOk = true;
                        }
                        $this->whereFunc($v,$id,$para,$isOk);
                    }
                }
            }else{
                $this->whereFunc($what,$id,$para);
            }
        }
    }

    function whereFunc($what, $id, $para, $isOr = false){
        if($what){
            if(is_array($id)){
                if($isOr){
                    $para->or_where_in($what, $id);
                }else{
                    $para->where_in($what, $id);
                }
            }else if (is_string($id) && $this->model_config['isSearch'] == true) {
                if($isOr){
                    $para->or_like($what, $id);
                }else{
                    $para->like($what, $id);
                }
            }else{
                if($isOr){
                    $para->or_where($what, $id);
                }else{
                    $para->where($what, $id);
                }
            }
        }else{
            $para->where($id);
        }
    }
    //endregion

    //region SETTING of CONFIG
    function setNoOr($isNoOr = false){
        $this->model_config['isNoOr'] = $isNoOr;
    }

    function setConfig($per_page = 10, $start = 0, $is_enable = false){
        $this->model_config['is_enable'] = $is_enable;
        $this->model_config['start'] = $start;
        $this->model_config['per_page'] = $per_page;
    }

    function setWhatNum($whatNum, $append = true){
        if($append){
            $this->model_config['whatNum'][] = $whatNum;
        }else{
            $this->model_config['whatNum'] = $whatNum;
        }
    }

    function setOrder($what = 'id', $order = 'ASC', $isNumber = false){
        $this->model_config['hasOrder'] = true;
        $this->model_config['theOrder'] = array(
            'what' => $what,
            'order' => $order
        );

        if($isNumber){
            $this->setWhatNum($what);
        }
    }

    function setProtectIdentifiers($isProtectIdentifiers = false){
        $this->model_config['isProtectIdentifiers'] = $isProtectIdentifiers;
    }

    function setSearch($isSearch = false){
        $this->model_config['isSearch'] = $isSearch;
    }

    function setLastId($whatField, $isLastInsert = true){
        $this->model_config['isLastInsert'] = $isLastInsert;
        $this->model_config['whatLastInsert'] = $whatField;
    }

    function setShift($isShift = true){
        $this->model_config['isShift'] = $isShift;
    }

    function setDistinct($selectFields = "", $escapeSelect = true, $isDistinct = true){
        $this->setSelectFields($selectFields, $escapeSelect);
        $this->model_config['isDistinct'] = $isDistinct;
    }

    function setSelectFields($selectFields = "", $escapeSelect = true){
        $this->model_config['selectFields'] = $selectFields;
        $this->model_config['escapeSelect'] = $escapeSelect;
    }

    function setConnectorArray($connectorArray = array()){
        $this->model_config['connectorArray'] = $connectorArray;
    }

    function setJoin($join_option = array()){
        $this->model_config['hasJoin'] = true;
        $this->model_config['theJoin'] = array_merge($this->model_config['theJoin'], $join_option);
    }

    function setForCount($isCount = true){
        $this->model_config['isCount'] = $isCount;
    }

    function setNoReset($noReset = true){
        $this->model_config['noReset'] = $noReset;
    }

    function setNormalized($normalizedFields = array(), $normalizedTo = array(), $normalizedToArray = false, $isNormalized = true){
        $this->model_config['isNormalized'] = $isNormalized;
        $this->model_config['normalizedFields'] = $normalizedFields;
        $this->model_config['normalizedTo'] = $normalizedTo;
        $this->model_config['normalizedToArray'] = $normalizedToArray;
    }

    function setReturnObject($isReturnObject = true){
        $this->model_config['isReturnObject'] = $isReturnObject;
    }

    function setReturnArray($isReturnArray = true){
        $this->model_config['isReturnArray'] = $isReturnArray;
    }

    function setTest($isTest = true){
        $this->model_config['isTest'] = $isTest;
    }

    function setGroupBy($groupBy = "", $hasGroupBy = true){
        $this->model_config['hasGroupBy'] = $hasGroupBy;
        $this->model_config['groupBy'] = $groupBy;
    }

    function setWithIncrement($isReturn = true, $fieldName = "ref", $fieldStart = 1){
        $this->model_config['returnWithIncrement']['isReturn'] = $isReturn;
        $this->model_config['returnWithIncrement']['fieldName'] = $fieldName;
        $this->model_config['returnWithIncrement']['fieldStart'] = $fieldStart;
    }
    //endregion

    function queryBinding($sql, $bind, $hasNoReturn = false){
        $query = $this->db->query($sql, $bind);

        if(!$hasNoReturn){
            $data = array();
            if($query->num_rows() > 0){
                foreach ($query->result() as $row){
                    $data[] = $row;
                }
            }
            return $data;
        }
    }

    function dataEscape($data){
        if(is_array($data)){
            if(count($data)>0){
                foreach($data as $k=>$v){
                    if(is_array($v)){
                        $v = $this->dataEscape($v);
                    }else{
                        $v = $this->db->escape($v);
                    }
                }
            }
        }else{
            $data = $this->db->escape($data);
        }

        return $data;
    }

    function randomizeReturn($isRandomized = false, $limitRandomTo = 1){
        $this->model_config['isRandomized'] = $isRandomized;
        $this->model_config['limitRandomTo'] = $limitRandomTo;
    }

    function encodeString($string, $key = ""){
        $key = !$key ? $this->config->config['encryption_key'] : $key;
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));

        return $encrypted;
    }

    function decodeString($string, $key = ""){
        $key = !$key ? $this->config->config['encryption_key'] : $key;
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");

        return $decrypted;
    }
}