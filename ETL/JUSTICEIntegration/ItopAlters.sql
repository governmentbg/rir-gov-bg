ALTER TABLE `itop`.`Buildings` 
ADD COLUMN `legacy_building_id` VARCHAR(45) NULL AFTER `updated_on`;

ALTER TABLE `itop`.`Rooms` 
ADD COLUMN `legacy_room_id` VARCHAR(45) NULL AFTER `org_id`;

INSERT INTO `itop`.`N_organization_types` (`name`, `status`, `code`, `created_on`, `updated_on`) 
VALUES 
('ОИВ Органи на изпълнителната власт', 'active', 'Executive authority', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Съд', 'active', 'Court', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Следствена служба', 'active', 'Investigation Office', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Прокуратура', 'active', 'Prosecution', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Апелативен район', 'active', 'Аppellate Area', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Арест', 'active', 'Arrest', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('ВД на ВАС', 'active', 'VD SAC', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('ВСС', 'active', 'SJC', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('ГДИН (централно управление)', 'active', 'GDIN (central control)', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Затвор', 'active', 'Prison', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Затворническо общежитие', 'active', 'Prison Hostel', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('И към ВСС', 'active', 'And SJC', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('МП', 'active', 'MJ', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('НИП', 'active', 'NIP', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('ОСИН', 'active', 'Osina', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Поправителен дом', 'active', 'Reformatory', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Пробация', 'active', 'Probation', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('ПС', 'active', 'PS', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('РИЦ', 'active', 'RIC', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Следствен отдел', 'active', 'investigation department', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Имотен рeгистър', 'active', 'Property register', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Служба по регистрация', 'active', 'Registry (Commercial, Rios, Bulstat pp)', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Търговски регистър', 'active', 'Commercial Registry', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('РИОС', 'active', 'RIOS', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('БУЛСТАТ регистър', 'active', 'BULSTAT Register', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('ОСВ Органи на съдебната власт', 'active', 'SIA judicial authority', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('ГДО Главна дирекция охрана', 'active', 'LTI General Directorate Security', '2018-09-25 16:53:26', '2018-09-25 16:53:26');

INSERT INTO `itop`.`N_organization_subtypes` (`name`, `N_organization_types_id`, `code`, `status`, `created_on`, `updated_on`) 
VALUES
('Върховен', '13', 'High Court', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Апелативен', '13', 'Court of Appeal', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Апелативен-специализиран наказателен', '13', 'Appeals specialized criminal court', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Специализиран наказателен', '13', 'Specialized Criminal Court', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Окръжен', '13', 'District Court', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Административен', '13', 'Administrative Court', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Военен', '13', 'Court-martial', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Районен', '13', 'Regional Court', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Апелативна', '15', 'Prosecution of Appeal', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Апелативна специализирана', '15', 'Appellate Specialized Prosecutor\'s Office', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Специализирана', '15', 'Specialized Prosecutor\'s Office', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Окръжна', '15', 'District Prosecutor\'s Office', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Военна', '15', 'Military Prosecutor', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26'),
('Районна', '15', 'Regional Prosecutors\' Office', 'active', '2018-09-25 16:53:26', '2018-09-25 16:53:26');

ALTER TABLE `itop`.`Contracts` 
ADD COLUMN `legacy_contract_id` VARCHAR(45) NULL AFTER `contract_number`;

ALTER TABLE `itop`.`Employee` 
ADD COLUMN `legacy_employee_id` VARCHAR(45) NULL AFTER `finalclass`;

ALTER TABLE `itop`.`Legal_entity` 
ADD COLUMN `legacy_legal_entity_id` VARCHAR(45) NULL AFTER `type`;

ALTER TABLE `itop`.`Asset` 
ADD COLUMN `legacy_asset_id` VARCHAR(45) NULL AFTER `legacy_nist_cpe`;













