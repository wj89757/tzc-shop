<?php
    function callSimsimi($keyword)
    {
        $params['key'] = "53b99c54-d876-4aad-a4b2-6bc1937c2194";
        $params['lc'] = "ch";
        $params['ft'] = "1.0";
        $params['text'] = $keyword;
        
        $url = "http://sandbox.api.simsimi.com/request.p?".http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $message = json_decode($output,true);
        $result = "";
        if ($message['result'] == 100){
            $result = $message['response'];
        }else{
            $result = $message['result']."-".$message['msg'];
        }
        return $result;
    }
?>