<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: *");
include_once("../../constants.php");


if ($_SERVER["REQUEST_METHOD"] == "GET"){

    

    $data = json_decode(file_get_contents("php://input"));
    $province_name = $data->province_name;
    $district_name = $data->district_name;
    $ward_name = $data->ward_name;

    $province_id = getProvinceId($province_name);

    $district_id = getDistrictID($province_id, $district_name);

    $ward_code = getWardCode($district_id, $ward_name);

    $total_fee = calculateFee($district_id, $ward_code);
    

    if ($total_fee > 0){
        $Msg = json_encode(['status'=> SUCCESS_RESPONSE, 
                                 'data'=>['total_fee' => $total_fee]]);
        echo $Msg;
    }else{
        throwMessage(CALCULATE_FEE_FAILED, "Can't calculate fee");
    }


}else {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}



function getProvinceID($cityName){
    $province_id = -1;
    $url_province = 'https://online-gateway.ghn.vn/shiip/public-api/master-data/province';
    
    $curlHandle = curl_init($url_province);

    curl_setopt_array($curlHandle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Token: 0a8e7e91-8da4-11ee-a59f-a260851ba65c']
    ]);

    $response = curl_exec($curlHandle);

    if ($response) {
        $responseData = json_decode($response);
        $data = $responseData->data;
        foreach($data as $dataItem){
            if (in_array($cityName, $dataItem->NameExtension)){
                $province_id = $dataItem->ProvinceID ;
                break;
            }
        }
    } else {
        echo 'Không thể nhận dữ liệu từ API';
    }
    curl_close($curlHandle);

    return $province_id;
}

function getDistrictID($province_id, $district_name){
    $district_id = -1;
    $url_district = 'https://online-gateway.ghn.vn/shiip/public-api/master-data/district';
    $jsonData = json_encode(['province_id' => $province_id]);

    $curlHandle = curl_init($url_district);

    curl_setopt_array($curlHandle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Token: 0a8e7e91-8da4-11ee-a59f-a260851ba65c']
    ]);

    $response = curl_exec($curlHandle);

    if ($response) {
        $responseData = json_decode($response);
        $data = $responseData->data;
        foreach($data as $dataItem){
            $lowercaseData = array_map('strtolower', $dataItem->NameExtension);
            $lowercaseSearchTerm = strtolower($district_name);
            if (in_array($lowercaseSearchTerm, $lowercaseData)){
                $district_id = $dataItem->DistrictID ;
                break;
            }
        }
    } else {
        echo 'Không thể nhận dữ liệu từ API';
    }
    curl_close($curlHandle);

    return $district_id;
}

function getWardCode($district_id, $ward_name){
    $ward_code = -1;
    $url_ward = 'https://online-gateway.ghn.vn/shiip/public-api/master-data/ward';
    $jsonData = json_encode(['district_id' => $district_id]);

    $curlHandle = curl_init($url_ward);

    curl_setopt_array($curlHandle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Token: 0a8e7e91-8da4-11ee-a59f-a260851ba65c']
    ]);

    $response = curl_exec($curlHandle);

    if ($response) {
        $responseData = json_decode($response);
        $data = $responseData->data;
        foreach($data as $dataItem){
            $lowercaseData = array_map('strtolower', $dataItem->NameExtension);
            $lowercaseSearchTerm = strtolower($ward_name);
            if (in_array($lowercaseSearchTerm, $lowercaseData)){
                $ward_code = $dataItem->WardCode ;
                break;
            }
        }
    } else {
        echo 'Không thể nhận dữ liệu từ API';
    }
    curl_close($curlHandle);

    return $ward_code;
}

function calculateFee($to_district_id, $to_ward_code){
    $total_fee = -1;
    $from_district_id = 3695;
    $from_ward_code = 90737;

    $url_ward = 'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee';
    $jsonData = json_encode([
        "from_district_id" => $from_district_id,
        "from_ward_code" => (string)$from_ward_code,
        "service_id" => 53320,
        "service_type_id" => null,
        "to_district_id" => $to_district_id,
        "to_ward_code" => (string)$to_ward_code,
        "height" => null,
        "length" => null,
        "weight" => 10000,
        "width" => null,
        "insurance_value" => 300000, 
        "cod_failed_amount" => 20000,
        "coupon" => null]);

    $curlHandle = curl_init($url_ward);

    curl_setopt_array($curlHandle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Token: 0a8e7e91-8da4-11ee-a59f-a260851ba65c']
    ]);

    $response = curl_exec($curlHandle);

    if ($response) {
        $responseData = json_decode($response);
        $data = $responseData->data;
        if ($responseData->code == 200){
            $total_fee = $data->total;
        }
    } else {
        echo 'Không thể nhận dữ liệu từ API';
    }
    curl_close($curlHandle);

    return $total_fee;
}
?>