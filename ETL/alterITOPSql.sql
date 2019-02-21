ALTER TABLE `staging`.`administration_units` 
ADD INDEX `admnstrtn_unts_dstrcts_cd_idx` (`district_ekatte_code` ASC),
ADD INDEX `admnstrtn_unts_mncplt_cd_idx` (`municipality_ekatte_code` ASC),
ADD INDEX `admnstrtn_unts_cts_cd_idx` (`settlement_ekatte_code` ASC),
ADD INDEX `admnstrtn_unts_rgns_cd_idx` (`area_ekatte_code` ASC),
ADD INDEX `admnstrtn_unts_orgnztn_unt_tp_cd_idx` (`adm_type` ASC);

ALTER TABLE `staging`.`administration` 
ADD COLUMN `related_iisda_id` VARCHAR(255) NULL AFTER `website`;

ALTER TABLE `staging`.`administration_services` 
ADD COLUMN `law_reason` TEXT NULL AFTER `section_name`,
ADD COLUMN `service_provision_level` VARCHAR(255) NULL AFTER `law_reason`;



