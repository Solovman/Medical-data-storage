CREATE TABLE `person` (
	                      `id` integer PRIMARY KEY,
	                      `full_name` varchar(255),
	                      `age` integer,
	                      `birth_day` timestamp,
	                      `gender` varchar(255),
	                      `phone_number` varchar(255),
	                      `role_id` integer
);

CREATE TABLE `role` (
	                    `id` integer PRIMARY KEY,
	                    `title` varchar(255)
);

CREATE TABLE `fact` (
	                    `person_id` integer,
	                    `health_group` float,
	                    `male_cvd_risk` bool,
	                    `age_cvd_risk_55m65f` bool,
	                    `menopause_cvd_risk` bool,
	                    `family_history_cvd_risk` bool,
	                    `family_history_mi_cvd_risk` bool,
	                    `family_history_stroke_cvd_risk` bool,
	                    `diabetes_cvd_risk` bool,
	                    `smoking_cvd_risk` bool,
	                    `smoking_duration_months` integer,
	                    `hypertension_cvd_risk` bool,
	                    `obesity_cvd_risk` bool,
	                    `alcohol_consumption_cvd_risk` bool,
	                    `diagnosis_ich_angina` bool,
	                    `diagnosis_acs_history` bool,
	                    `diagnosis_hypertension` bool,
	                    `six_minute_walk_distance` float,
	                    `nyha_class_hfn_test` integer,
	                    `height` integer,
	                    `weight` integer,
	                    `bmi` float,
	                    `obesity_grade_bmi` integer,
	                    `waist_circumference` float,
	                    `systolic_bp` integer,
	                    `diastolic_bp` integer,
	                    `heart_rate` integer,
	                    `score_index` float,
	                    `framingham_index` integer,
	                    `total_cholesterol` float,
	                    `hdl_cholesterol` float,
	                    `ldl_cholesterol` float,
	                    `non_hdl_cholesterol` float,
	                    `triglycerides` float,
	                    `atherogenicity_coefficient` float,
	                    `c_reactive_protein` float,
	                    `sedimentation_rate` integer,
	                    `hemoglobin_concentration` integer,
	                    `hematocrit` float,
	                    `platelet_count` integer,
	                    `creatinine` float,
	                    `glucose` float,
	                    `urine_albumin_to_creatinine_ratio` float,
	                    `vitamin_d_level` float,
	                    `angioscan` timestamp,
	                    `vessel_wall_elasticity` float,
	                    `endothelium_dependent_vasodilation` float,
	                    `echocardiography` timestamp,
	                    `aorta_sinus` integer,
	                    `left_atrium` varchar(255),
	                    `common_carotid_artery` float,
	                    `left_ventricle_mass` float,
	                    `left_ventricular_mass` float,
	                    `interventricular_septum_thickness` integer,
	                    `left_ventricular_posterior_wall_thickness` integer,
	                    `ejection_fraction` integer,
	                    `myocardial_hypertrophy` integer,
	                    `concentric_remodeling` bool,
	                    `atherosclerotic_plaque_presence` bool,
	                    `intima_media_thickness` float,
	                    `angiometry` timestamp,
	                    `coll_adp` integer,
	                    `coll_epi` integer,
	                    `p2y12` integer,
	                    `adp_ohm` integer,
	                    `collagen_ohm` integer,
	                    `arach_ac_ohm` integer,
	                    `sers` bool,
	                    `surface_type` varchar(255),
	                    `note_` varchar(255),
	                    `collection_date` timestamp,
	                    `imaging_date` timestamp,
	                    `smoker_index` float
);

CREATE TABLE `migration` (
	                         `id` integer,
	                         `title` varchar(255)
);

ALTER TABLE `fact` ADD FOREIGN KEY (`person_id`) REFERENCES `person` (`id`);

ALTER TABLE `person` ADD FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
