<?php
require_once __DIR__ . '/vendor/autoload.php';
use xtype\Eos\Client;

/*
Github: https://github.com/svenpohl
sven.pohl@zen-systems.de


Usage:
Libs:
This script needs an instance of eos-client for signing Transactions:
https://github.com/svenpohl/eos-client

--- setup two local cronjob2
5 6 * * * php /localpath/cpubot.php >/dev/null 2>&1

15 6 * * * php /localpath/cpubot.php check >/dev/null 2>&1
---
*/

date_default_timezone_set('Europe/Berlin');


if ( isset($argv[1]) )
   {
   $mode = $argv[1];
   } else $mode = "";

print("\nMode: $mode \n");

$cpubotcpubot = "cpubotcpubot";
$day = getdayindex();
print("Day: " . $day . " \n<br>");     
if ( ($day % 2) == 0) $cpubotcpubot = "mycpubot0001"; else $cpubotcpubot = "mycpubot0001";
         
$privkey      = "5Jwt1P7hgZeZH2WPk2mfCuxxxxxxfkfwdjxk9wJMGaeXoYYsDv";


$client = new Client('https://eos.greymass.com');



$logfile = __DIR__."/error.log";
print("log: $logfile ");

if ( !file_exists($logfile) )
   {
   $handle2 = fOpen($logfile , "wb");
   $buffer2 = "INIT\n";
   fWrite($handle2, $buffer2);
   fClose($handle2);	
   }


// Create Blacklist and Whitelist
$array_blacklist    = [];
//$array_blacklist[0] = "blacklisted1";
//$array_blacklist[1] = "blacklisted1";
 

$array_whitelist    = [];
$array_whitelist[0] = "testaccount1";
$array_whitelist[1] = "testaccount2";
 

 

 
$accountarray    = [];
$acindex		 = 0;

// Add whitelist
$size2 = count( $array_whitelist );
for ($i=0; $i<$size2; $i++)
    {
    $account = $array_whitelist[$i];
    $amount  = "0.0010 EOS";
    
    $accountarray[$acindex]['account'] = $account;
    $accountarray[$acindex]['amount']  = $amount;
    $acindex++;
    } // for i...

print("accountarray: \n");
print_r($accountarray);
// --- END Accountarray


 

 

 
 
if ($mode == "check")
   {
   print("C H E C K \n");
   

   // Check BOT-CPU
   $account = $cpubotcpubot;
   $cpu = getcpu( $account );
   print("cpu:  ".$cpu." \n");
   if ($cpu <= 1000)
      {
      $amount  = "0.0250 EOS";
      powerup($cpubotcpubot,$logfile, $client, $account, $amount, $privkey);
      } else
        {
        print("CPU of ". $cpubotcpubot. " is OK! \n");
        }
   sleep(2);
   
   
    
  
$size = count( $accountarray );
    
for ($i=0; $i<$size; $i++)
    {
    
    $account = $accountarray[$i]['account'];
    $amount  = $accountarray[$i]['amount'];
    
    $buffer = "account: " . $account . "\n";
    
    
    $cpu = getcpu( $account );
    print("cpu:  ".$cpu." \n");
    if ($cpu <= 1000)
       {
       $buffer = "[CHECK] CPU of ". $account. " needs Refresh! \n";
       print($buffer); 
       addlog($logfile,$buffer);
       powerup($cpubotcpubot,$logfile, $client, $account, $amount, $privkey);
       } else
         {
         $buffer = "[CHECK] CPU of ". $account. " is OK! \n";
         print($buffer); 

         }
    
 
    sleep(2);
    // ---
 
 
 
 
    print("account: " . $account . "\n");
    } // for i...

   print("FIN CHECK \n");
   exit(0); 
   } // if ($mode == "check") 
 
 
 
  





// First refresh bot-CPU
$account = $cpubotcpubot;
$amount  = "0.0250 EOS";
powerup($cpubotcpubot,$logfile, $client, $account, $amount, $privkey);
sleep(3); 
 
 
 


$size = count( $accountarray );
 
for ($i=0; $i<$size; $i++)
    {
    $account = $accountarray[$i]['account'];
    $amount  = $accountarray[$i]['amount'];    
        
    powerup($cpubotcpubot,$logfile, $client, $account, $amount, $privkey);
 
    sleep(2);
 
    print("account: " . $account . "\n");
    } // for i...







print("FIN\n");
exit(0); 



//
// Functions
//

function getcpu($account)
{
print("get...\n");

//$url = "http://eos.greymass.com/v1/chain/get_table_rows";
$url = "http://eos.greymass.com/v1/chain/get_account";
 
$data_string = '{"account_name":"'.$account.'","json":"true"}';
 print($data_string);
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");    			
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$timeout = 105;
$timeout = 5;
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

$file_contents = curl_exec($ch);
curl_close($ch);    
 
$json_array = json_decode($file_contents, true);

$cpu = ($json_array['cpu_limit']['max']);

print("cpu: $cpu \n");

return($cpu);
} // getcpu()

 
 

function powerup($cpubotcpubot,$logfile, $client, $account, $amount, $privkey)
{


    // --- Powerup Calc - SELF (cpubotcpubot)
    $tx = $client->addPrivateKeys([$privkey])->transaction([
    'actions' => [
        [
            'account' => 'eosio.token',
            'name' => 'transfer',
            'authorization' => [[
                'actor' => $cpubotcpubot,
                'permission' => 'active',
            ]],
            'data' => [
                'from' => $cpubotcpubot,
                'to' => 'powerupcalc1',
                'quantity' => $amount,
                'memo' => $account,
            ],
        ]
      ]
         ]);


    $buffer = "(".$account.") Transaction ID: {$tx->transaction_id} \n";
    echo $buffer;
 
    addlog($logfile, $buffer );
    
    
} // powerup();
 





function getdayindex()
{

$now = new DateTime();
$zero = new DateTime('0000-00-03'); 
$diff = $now->diff($zero);

$ret = $diff->format('%a');
return($ret);
} // function getdayindex()

 




function addlog($logfile,$buffer)
{
$handle2 = fOpen($logfile , "a");
fWrite($handle2, $buffer);
fClose($handle2);	
} // addlog   
 

// EOF
?>
