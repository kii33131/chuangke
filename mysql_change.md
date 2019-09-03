##2019-06-12 huiting
#mysql

```
ALTER TABLE `tbl_achievement`
ADD COLUMN `pt_money`  decimal(10,2) NOT NULL DEFAULT 0 COMMENT '平台应收金额' AFTER `ptconfirm_time`;

CREATE TABLE `tbl_payment_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL DEFAULT '0',
  `money` decimal(10,2) NOT NULL,
  `voucher` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `achievement_id` int(11) NOT NULL DEFAULT '0' COMMENT '结算id',
  `bank_id` int(11) NOT NULL DEFAULT '0' COMMENT '平台银行卡id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `tbl_business`
ADD COLUMN `is_delete`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '1删除' AFTER `member_id`;




````


### 2019-08-9
###mysql

```$xslt

ALTER TABLE `tbl_task` 
ADD COLUMN `type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0 待审核 1审核通过 2 不通过' AFTER `business_id`;

ALTER TABLE `tbl_task` 
ADD COLUMN `reasons` varchar(255) NOT NULL DEFAULT '' COMMENT '驳回原因' AFTER `type`;

ALTER TABLE `chuangke`.`tbl_task` 
ADD COLUMN `lower_shelf` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0不下架 1下架' AFTER `reasons`;

```



