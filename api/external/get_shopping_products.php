<?php
include "./init.php";

if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_shopping_products")
  {
    /// get all internet products
      $query="SELECT * FROM `products` WHERE `category`='internet' AND `status`='active'";
      $stmt1 = $dbTools->getConnection()->prepare($query);
      $stmt1->execute();
      $internet_products_result = $stmt1->get_result();
      $internet_products=[];
      while($internet_product_row = $dbTools->fetch_assoc($internet_products_result))
      {
        array_push($internet_products,$internet_product_row);
      }


      //get all phone products
      $query="SELECT * FROM `products` WHERE `category`='phone' AND `status`='active'";
      $stmt1 = $dbTools->getConnection()->prepare($query);
      $stmt1->execute();
      $phone_products_result = $stmt1->get_result();
      $phone_products=[];
      while($phone_product_row = $dbTools->fetch_assoc($phone_products_result))
      {
        array_push($phone_products,$phone_product_row);
      }
      /// get modems in inventory
      $query="SELECT * FROM `modems` WHERE `reseller_id`=190";
      $stmt1 = $dbTools->getConnection()->prepare($query);
      $stmt1->execute();
      $modems_result = $stmt1->get_result();
      $modems=[];
      while($row_modem = $dbTools->fetch_assoc($modems_result))
      {
        $modem["name"]= $row_modem["mac_address"] . "[" . $row_modem["type"] . " | " . $row_modem["serial_number"] . "]";
        $modem["value"]=$row_modem["modem_id"];
        array_push($modems,$modem);
      }
      $modems_json = json_encode($modems);

      //get phone provinces
      $provinces=[];
        $province["name"]="ONTARIO (ON)";
        $province["value"]="ON";
        array_push($provinces,$province);
        $province["name"]="QUEBEC (QC)";
        $province["value"]="QC";
        array_push($provinces,$province);
        $province["name"]="ALBERTA (AB)";
        $province["value"]="AB";
        array_push($provinces,$province);
        $province["name"]="BRITISH COLUMBIA (BC)";
        $province["value"]="BC";
        array_push($provinces,$province);
        $province["name"]="MANITOBA (MB)";
        $province["value"]="MB";
        array_push($provinces,$province);
        $province["name"]="NOVA-SCOTIA (NS)";
        $province["value"]="NS";
        array_push($provinces,$province);
        $province["name"]="NEWFOUNDLAND (NL)";
        $province["value"]="NL";
        array_push($provinces,$province);
      $provinces_json = json_encode($provinces);
      //////// get other internet providers
      $providers=[];
        $provider["name"]="Acanac";
        $provider["value"]="Acanac";
        array_push($providers,$provider);
        $provider["name"]="ACN";
        $provider["value"]="ACN";
        array_push($providers,$provider);
        $provider["name"]="B2B2C";
        $provider["value"]="B2B2C";
        array_push($providers,$provider);
        $provider["name"]="CIK";
        $provider["value"]="CIK";
        array_push($providers,$provider);
        $provider["name"]="Distributel";
        $provider["value"]="Distributel";
        array_push($providers,$provider);
        $provider["name"]="Electronibox";
        $provider["value"]="Electronibox";
        array_push($providers,$provider);
        $provider["name"]="iTalk BB";
        $provider["value"]="iTalk BB";
        array_push($providers,$provider);
        $provider["name"]="Rogers";
        $provider["value"]="Rogers";
        array_push($providers,$provider);
        $provider["name"]="Shaw";
        $provider["value"]="Shaw";
        array_push($providers,$provider);
        $provider["name"]="TekSavvy";
        $provider["value"]="TekSavvy";
        array_push($providers,$provider);
        $provider["name"]="Videotron";
        $provider["value"]="videotron";
        array_push($providers,$provider);
        $provider["name"]="Altimatel";
        $provider["value"]="altimatel";
        array_push($providers,$provider);
        $provider["name"]="James Telecom";
        $provider["value"]="jamestelecom";
        array_push($providers,$provider);
        $provider["name"]="Other";
        $provider["value"]="other";
        array_push($providers,$provider);
        $providers_json = json_encode($providers);

      $internet_products_json = json_encode($internet_products);
      $phone_products_json = json_encode($phone_products);

      echo "{\"internet\":{"
                        , "\"products\":",$internet_products_json
                        , ",\"related_services\":{"
                          , "\"modems\":["
                                ,"{"
                                  , "\"name\":\"Free Rent Modem ($59.90 deposit)\""
                                  , ",\"price\":0"
                                  , ",\"value\":\"rent\""
                                , "}"
                                ,",{"
                                  , "\"name\":\"Buy Business Modem\""
                                  , ",\"price\":200"
                                  , ",\"value\":\"buy\""
                                , "}"
                                ,",{"
                                  , "\"name\":\"Reseller Inventory\""
                                  , ",\"price\":59.90"
                                  , ",\"value\":\"inventory\""
                                , "}"
                                ,",{"
                                  , "\"name\":\"I have my own modem\""
                                  , ",\"price\":0"
                                  , ",\"value\":\"own_modem\""
                                , "}"
                                ,"]"
                          , ",\"routers\":["
                                ,"{"
                                  , "\"name\":\"Rent WIFI Router MikroTik Hap Series\""
                                  , ",\"price\":2.90"
                                  , ",\"value\":\"rent\""
                                , "}"
                                ,",{"
                                  , "\"name\":\"Rent WIFI Router MikroTik Hap lite\""
                                  , ",\"price\":4.90"
                                  , ",\"value\":\"rent_hap_lite\""
                                , "}"
                                ,",{"
                                  , "\"name\":\"Buy WIFI Router MikroTik Hap ac lite\""
                                  , ",\"price\":74.00"
                                  , ",\"value\":\"buy_hap_ac_lite\""
                                , "}"
                                ,",{"
                                  , "\"name\":\"Buy WIFI Router MikroTik Hap mini\""
                                  , ",\"price\":39.90"
                                  , ",\"value\":\"buy_hap_mini\""
                                , "}"
                                ,",{"
                                  , "\"name\":\"I don't need a router\""
                                  , ",\"price\":0"
                                  , ",\"value\":\"dont_need\""
                                , "}"
                                ,"]"
                          , ",\"additional_service\":"
                                ,"{"
                                  , "\"name\":\"Additional Services\""
                                  , ",\"price\":5"
                                  , ",\"value\":[\"yes\",\"no\"]"
                                , "}"
                          , ",\"static_ip\":"
                                ,"{"
                                  , "\"name\":\"Static IP\""
                                  , ",\"price\":20"
                                  , ",\"value\":[\"yes\",\"no\"]"
                                , "}"
                        , "}"
                        , ",\"extra_needed_fields\":"
                              ,"{"
                                , "\"providers\":",$providers_json
                                , ",\"inventory_modems\":",$modems_json
                                , ",\"plans\":["
                                      ,"{"
                                        , "\"name\":\"Monthly Payment ($60.00 New Installation Fees $19.90 Transfer Fees for current Cable subscriber\""
                                        , ",\"value\":\"monthly\""
                                      , "}"
                                      ,",{"
                                        , "\"name\":\"Yearly Contract, Payment Monthly (Free Installation)\""
                                        , ",\"value\":\"yearly\""
                                      , "}"
                                  ,"]"
                              , "}"
        , "}"
        , ",\"phone\":{"
                          , "\"products\":",$phone_products_json
                          , ",\"related_services\":{"
                            , "\"adapter\":["
                                  ,"{"
                                    , "\"name\":\"I have my own Phone Adapter\""
                                    , ",\"price\":0"
                                    , ",\"value\":\"my_own\""
                                  , "}"
                                  ,",{"
                                    , "\"name\":\"Buy Cisco SPA112 2-Port Phone Adapter \""
                                    , ",\"price\":59.90"
                                    , ",\"value\":\"buy_Cisco_SPA112\""
                                  , "}"
                                  ,"]"
                            , ",\"you_have_phone_number\":["
                                  ,"{"
                                    , "\"name\":\"Transfer current number\""
                                    , ",\"price\":15"
                                    , ",\"value\":\"yes\""
                                  , "}"
                                  ,",{"
                                    , "\"name\":\"New phone number\""
                                    , ",\"price\":0"
                                    , ",\"value\":\"no\""
                                  , "}"

                                  ,"]"
                          , "}"
                          , ",\"extra_needed_fields\":"
                                ,"{"
                                  , "\"phone_provinces\":",$provinces_json

                                , "}"
                          , "}"
        , ",\"message\":\"\""
        , ",\"error\":false}";




  }// end get_shopping_products
}
else {
  echo "{\"internet\":{}"
    , ",\"phone_products\":{}"
    , ",\"message\":\" you are not authorized\""
    , ",\"error\":true}";
}

?>
