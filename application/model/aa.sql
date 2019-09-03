 SET NAMES utf8mb4;
    SET FOREIGN_KEY_CHECKS = 0;

    -- ----------------------------
    -- Table structure for tbl_achievement
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_achievement`;
    CREATE TABLE `tbl_achievement`  (
      `id` int(11) NOT NULL,
      `member_task_id` int(11) NOT NULL DEFAULT 0 COMMENT '领取信息id',
      `number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '结算单号',
      `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0 待结算 1:待收款 2:以收款',
      `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
      `commission` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '任务佣金',
      `tax` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '代征税金',
      `tax_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '税金支付方 1:企业支付 2：创客支付',
      `service_money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '服务费',
      `service_pay_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1:企业支付 2创客支付',
      `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '收款总额',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '业绩表' ;

    -- ----------------------------
    -- Table structure for tbl_business
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_business`;
    CREATE TABLE `tbl_business`  (
      `id` int(11) UNSIGNED NOT NULL,
      `abbreviation` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '简称',
      `taxpayer` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '纳税人识别号',
      `license` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '企业执照',
      `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地址',
      `contacts` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '联系人',
      `contacts_mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '联系人电话',
      `business_mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '企业电话',
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '企业名称',
      `legal_person` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '法人',
      `legal_person_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '法人身份证',
      `bank_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '基本开户银行名称',
      `subbranch_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '支行名称',
      `card_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '银行卡号',
      `bank_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '开户所在地',
      `id_img_frontal` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '身份证正面',
      `id_img_back` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '身份证反面',
      `open_permit` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '开户许可证',
      `invoice_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1:增值税普通发票2:增值税专用发票',
      `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1:待审核 2：审核通过 3：审核失败',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '企业表';

    -- ----------------------------
    -- Table structure for tbl_business_member
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_business_member`;
    CREATE TABLE `tbl_business_member`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
      `business_id` int(11) NOT NULL COMMENT '企业id',
      `created_at` datetime NULL DEFAULT NULL COMMENT '添加时间',
      `is_delete` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1：删除',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '企业创客表' ;

    -- ----------------------------
    -- Table structure for tbl_channel
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_channel`;
    CREATE TABLE `tbl_channel`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '渠道名称',
      `is_delete` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1:删除',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ;

    -- ----------------------------
    -- Table structure for tbl_config
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_config`;
    CREATE TABLE `tbl_config`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '平台开户名称',
      `number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '平台银行卡号',
      `bank_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '银行名称',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ;

    -- ----------------------------
    -- Table structure for tbl_member
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_member`;
    CREATE TABLE `tbl_member`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `account` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '登陆账号',
      `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '绑定手机号',
      `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地址',
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
      `cart_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '身份证id',
      `id_img_frontal` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '正面',
      `id_img_back` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '反面',
      `status` int(11) NOT NULL DEFAULT 0 COMMENT '1:审核通过2:拒绝',
      `is_authentication` tinyint(11) NOT NULL DEFAULT 0 COMMENT '1:认证通过2：拒绝 0:待认证',
      `created_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '创客表' ;

    -- ----------------------------
    -- Table structure for tbl_member_bank
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_member_bank`;
    CREATE TABLE `tbl_member_bank`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `bank_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '开户行',
      `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
      `sub_branch` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '支行',
      `number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '银行卡号',
      `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地址',
      `created_at` datetime NULL DEFAULT NULL,
      PRIMARY KEY (`id`) USING BTREE,
      INDEX `member_id`(`member_id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '创客银行卡' ;

    -- ----------------------------
    -- Table structure for tbl_member_channel
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_member_channel`;
    CREATE TABLE `tbl_member_channel`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
      `channel_id` int(11) NOT NULL DEFAULT 0 COMMENT '技能点',
      `created_at` datetime NULL DEFAULT NULL,
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '创客技能表' ;

    -- ----------------------------
    -- Table structure for tbl_member_schedule
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_member_schedule`;
    CREATE TABLE `tbl_member_schedule`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户信息',
      `start_time` datetime NOT NULL COMMENT '执行日期开始',
      `end_time` datetime NOT NULL COMMENT '执行日期结束',
      `task_id` int(11) NOT NULL DEFAULT 0 COMMENT '任务id',
      `created_at` datetime NULL DEFAULT NULL COMMENT '添加时间',
      `member_task_id` int(11) NOT NULL DEFAULT 0 COMMENT '创客任务id',
      `completion_degree` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '业绩完成度',
      `basis` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '申请依据',
      `is_delete` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1已删除',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '业绩进度表' ;

    -- ----------------------------
    -- Table structure for tbl_member_task
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_member_task`;
    CREATE TABLE `tbl_member_task`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
      `task_id` int(11) NOT NULL DEFAULT 0 COMMENT '任务id',
      `business_id` int(11) NOT NULL DEFAULT 0 COMMENT '企业id',
      `status` int(11) NOT NULL DEFAULT 0 COMMENT '审核状态 0:待审核1:审核通过2:驳回',
      `created_at` datetime NULL DEFAULT NULL,
      `examine_at` datetime NULL DEFAULT NULL,
      `opinion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '审核意见',
      `type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0正常领取1:企业指派',
      `response_at` datetime NULL DEFAULT NULL COMMENT '创客响应时间',
      `response_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0：待同意 1:同意2:不同意',
      `number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '业务单号',
      `achievement` tinyint(2) NOT NULL COMMENT '0 : 没有发布业绩 1：发布业绩待审核 2：驳回业绩 4：同意业绩',
      `reasons_rejection` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '驳回原因',
      PRIMARY KEY (`id`) USING BTREE,
      INDEX `member_id`(`member_id`) USING BTREE,
      INDEX `task_id`(`task_id`) USING BTREE,
      INDEX `business_id`(`business_id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '创客任务表' ;

    -- ----------------------------
    -- Table structure for tbl_settlement
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_settlement`;
    CREATE TABLE `tbl_settlement`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '创客id',
      `business_id` int(11) NOT NULL DEFAULT 0 COMMENT '企业id',
      `schedule_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联业绩id',
      `creared_at` datetime NULL DEFAULT NULL COMMENT '生成时间',
      `number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '单号',
      `behalf_taxation` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1:企业支付税金2:创客支付税金',
      `service_charge` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1:企业支付2：创客支付',
      `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0:待结算1:提交平台审核2：平台审核通过(企业可以支付)3:企业确认支付4：用户确认收款',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '结算表' ;

    -- ----------------------------
    -- Table structure for tbl_task
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_task`;
    CREATE TABLE `tbl_task`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '任务名称',
      `channel_id` int(11) NOT NULL DEFAULT 0 COMMENT '渠道id',
      `num` int(11) NOT NULL DEFAULT 0 COMMENT '任务数量',
      `price` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '单价',
      `commission` decimal(10, 2) NOT NULL COMMENT '任务佣金',
      `explain` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '任务说明',
      `audit_criteria` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '审核标准',
      `push_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '发布方式 1:不指定创客 2：制定创客 3:指定创客群',
      `number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '任务单号',
      `created_at` datetime NULL DEFAULT NULL COMMENT '发布时间',
      `update_at` datetime NULL DEFAULT NULL COMMENT '修改时间',
      `is_delete` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1：删除 0 :正常',
      `enclosure` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT '附件',
      PRIMARY KEY (`id`) USING BTREE,
      INDEX `channel_id`(`channel_id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ;

    -- ----------------------------
    -- Table structure for tbl_task_channel
    -- ----------------------------
    DROP TABLE IF EXISTS `tbl_task_channel`;
    CREATE TABLE `tbl_task_channel`  (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `task_id` int(11) NOT NULL DEFAULT 0 COMMENT '任务id',
      `channel_id` int(11) NOT NULL DEFAULT 0 COMMENT '渠道id',
      `created_at` datetime NULL DEFAULT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`) USING BTREE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '指定信息创客群表' ;

    SET FOREIGN_KEY_CHECKS = 1;
