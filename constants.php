<?php 
	/*Security*/
	define('SECRET_KEY', 'Z8EuwBmY5eEWhh8W4EOPJPPG8');
	
	/*Data Type*/
	define('BOOLEAN', 	'1');
	define('INTEGER', 	'2');
	define('STRING', 	'3');

	/*Error Codes*/
	define('REQUEST_METHOD_NOT_VALID',		        100);
	define('REQUEST_CONTENTTYPE_NOT_VALID',	        101);
	define('REQUEST_NOT_VALID', 			        102);
    define('VALIDATE_PARAMETER_REQUIRED', 			103);
	define('VALIDATE_PARAMETER_DATATYPE', 			104);
	define('API_NAME_REQUIRED', 					105);
	define('API_PARAM_REQUIRED', 					106);
	define('API_DOST_NOT_EXIST', 					107);
	define('INVALID_USER_PASS', 					108);
	define('USER_NOT_ACTIVE', 						109);
    define('SEND_EMAIL_ERROR', 						110);
    define('INVALID_EMAIL', 			    		111);
    define('INVALID_DATA_INPUT', 		    		112);
    define('CALCULATE_FEE_FAILED', 		    		113);
    define('DISCOUNT_NOT_VALID', 		    		114);
    define('OLD_PASSWORD_NOT_VALID', 		    	115);

	define('SUCCESS_RESPONSE', 						200);

	/*Server Errors*/

	define('JWT_PROCESSING_ERROR',					300);
	define('ATHORIZATION_HEADER_NOT_FOUND',			301);
	define('ACCESS_TOKEN_ERRORS',					302);	
    define('TIME_EXPIRED',		        			303);

    define('FAILED_UPDATE_PRODUCT_TO_CART', 	    401);
    define('FAILED_DELETE_PRODUCT_TO_CART', 	    402);
    define('FAILED_ADD_PRODUCT_TO_CART', 		    403);
    define('NOT_FOUND', 						    404);
    define('FAILED_ORDER',                  	    405);
    define('FAILD_CANCEL_ORDER',                    406);
    define('FAILED_SIGN_UP',                        407);


    function validateParameter($fieldName, $value, $dataType, $required = true) {
        if($required == true && empty($value) == true) {
            throwMessage(VALIDATE_PARAMETER_REQUIRED, $fieldName . " parameter is required.");
        }

        switch ($dataType) {
            case BOOLEAN:
                if(!is_bool($value)) {
                    throwMessage(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be boolean.');
                }
                break;
            case INTEGER:
                if(!is_numeric($value)) {
                    throwMessage(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be numeric.');
                }
                break;

            case STRING:
                if(!is_string($value)) {
                    throwMessage(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be string.');
                }
                break;
            
            default:
                throwMessage(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName);
                break;
        }

        return $value;

    }

    function throwMessage($code, $message) {
        header("content-type: application/json");
        $Msg = json_encode(['status'=>$code, 
                                 'message'=>$message]);
        echo $Msg; exit;
    }


?>