# cpubot
PHP Script for a private eosin/antelope cpu bot for unlimited CPU 


Github: https://github.com/svenpohl
sven.pohl@zen-systems.de


Usage:

This script needs an instance of eos-client for signing Transactions:
https://github.com/svenpohl/eos-client

Setup two local cronjobs:
´´´
5 6 * * * php /localpath/cpubot.php >/dev/null 2>&1

15 6 * * * php /localpath/cpubot.php check >/dev/null 2>&1
´´´

You will need two different cpu-spending accounts with some EOS and the same private key, like *mycpubot0001* and *mycpubot0002*

This accounts will be alterate each day, so you can be sure each account will have no negative CPU. Otherwise the cpu-refresh will fail.

This script will call the "powerupcalc1"-contract from the EOS-SOV team. By using this contract you just need to send an small amount of EOS, like 0.0010 EOS with the target-account for CPU in the memo.

*Transfer 0.0010 EOS mycpubot0001 powerupcalc1 testaccount1*


