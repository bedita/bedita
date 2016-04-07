ALTER TABLE `hash_jobs` MODIFY `status` VARCHAR(20) NOT NULL default 'pending' COMMENT 'job status, can be pending/in progress/expired/closed/failed';
ALTER TABLE `hash_jobs` ADD `result` TEXT COMMENT '(JSON) job result data';
 
