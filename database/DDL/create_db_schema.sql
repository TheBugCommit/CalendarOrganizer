CREATE TABLE nations (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
code varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
name varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
CONSTRAINT pk_nations PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
name varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
CONSTRAINT pk_roles PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
name varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
email varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
password varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
surname1 varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
surname2 varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
locked tinyint(1) NOT NULL DEFAULT 0,
birth_date date NOT NULL,
phone varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
gender char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
role_id bigint(20) unsigned NOT NULL DEFAULT 2,
nation_id bigint(20) unsigned NOT NULL,
google_access_token_json text,
remember_token varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
CONSTRAINT pk_users PRIMARY KEY (id),
UNIQUE KEY users_email_unique (email),
KEY users_nation_id_foreign (nation_id),
KEY users_roles_id_foreign (role_id),
CONSTRAINT users_roles_id_foreign FOREIGN KEY (role_id) REFERENCES roles (id),
CONSTRAINT users_nation_id_foreign FOREIGN KEY (nation_id) REFERENCES nations (id),
CONSTRAINT users_gender_check check (gender in ('M', 'F', 'O'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE categories (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
user_id bigint(20) unsigned NOT NULL,
name varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
CONSTRAINT pk_categories PRIMARY KEY (id),
KEY categories_user_id_foreign (user_id),
CONSTRAINT categories_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE calendars (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
user_id bigint(20) unsigned NOT NULL,
title varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
description varchar(200) COLLATE utf8mb4_unicode_ci,
google_calendar_id varchar(300) COLLATE utf8mb4_unicode_ci,
start_date date NOT NULL,
end_date date NOT NULL,
CONSTRAINT pk_calendars PRIMARY KEY (id),
KEY calendars_user_id_foreign (user_id),
CONSTRAINT calendars_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE events (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
calendar_id bigint(20) unsigned NOT NULL,
category_id bigint(20) unsigned NOT NULL,
user_id bigint(20) unsigned NOT NULL,
title varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
description varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
location varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
published tinyint(1) NOT NULL DEFAULT 0,
color varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
start datetime NOT NULL,
end datetime NOT NULL,
google_event_id varchar(1024) COLLATE utf8mb4_unicode_ci,
CONSTRAINT pk_events PRIMARY KEY (id),
KEY events_user_id_foreign (user_id),
KEY events_calendar_id_foreign (calendar_id),
KEY events_category_id_foreign (category_id),
CONSTRAINT events_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT events_calendar_id_foreign FOREIGN KEY (calendar_id) REFERENCES calendars (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT events_category_id_foreign FOREIGN KEY (category_id) REFERENCES categories (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE targets (
calendar_id bigint(20) unsigned NOT NULL,
email varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
CONSTRAINT pk_targets PRIMARY KEY (calendar_id,email),
CONSTRAINT targets_calendar_id_foreign FOREIGN KEY (calendar_id) REFERENCES calendars (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE calendar_user (
user_id bigint(20) unsigned NOT NULL,
calendar_id bigint(20) unsigned NOT NULL,
CONSTRAINT pk_calendar_user PRIMARY KEY (user_id,calendar_id),
KEY calendar_user_calendar_id_foreign (calendar_id),
CONSTRAINT calendar_user_calendar_id_foreign FOREIGN KEY (calendar_id) REFERENCES calendars (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT calendar_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Nations initialization

INSERT INTO nations (name, code) VALUES ('Afghanistan', 'AF');
INSERT INTO nations (name, code) VALUES ('Albania', 'AL');
INSERT INTO nations (name, code) VALUES ('Algeria', 'DZ');
INSERT INTO nations (name, code) VALUES ('American Samoa', 'AS');
INSERT INTO nations (name, code) VALUES ('Andorra', 'AD');
INSERT INTO nations (name, code) VALUES ('Angola', 'AO');
INSERT INTO nations (name, code) VALUES ('Anguilla', 'AI');
INSERT INTO nations (name, code) VALUES ('Antarctica', 'AQ');
INSERT INTO nations (name, code) VALUES ('Antigua and Barbuda', 'AG');
INSERT INTO nations (name, code) VALUES ('Argentina', 'AR');
INSERT INTO nations (name, code) VALUES ('Armenia', 'AM');
INSERT INTO nations (name, code) VALUES ('Aruba', 'AW');
INSERT INTO nations (name, code) VALUES ('Australia', 'AU');
INSERT INTO nations (name, code) VALUES ('Austria', 'AT');
INSERT INTO nations (name, code) VALUES ('Azerbaijan', 'AZ');
INSERT INTO nations (name, code) VALUES ('Bahamas', 'BS');
INSERT INTO nations (name, code) VALUES ('Bahrain', 'BH');
INSERT INTO nations (name, code) VALUES ('Bangladesh', 'BD');
INSERT INTO nations (name, code) VALUES ('Barbados', 'BB');
INSERT INTO nations (name, code) VALUES ('Belarus', 'BY');
INSERT INTO nations (name, code) VALUES ('Belgium', 'BE');
INSERT INTO nations (name, code) VALUES ('Belize', 'BZ');
INSERT INTO nations (name, code) VALUES ('Benin', 'BJ');
INSERT INTO nations (name, code) VALUES ('Bermuda', 'BM');
INSERT INTO nations (name, code) VALUES ('Bhutan', 'BT');
INSERT INTO nations (name, code) VALUES ('Bosnia and Herzegovina', 'BA');
INSERT INTO nations (name, code) VALUES ('Botswana', 'BW');
INSERT INTO nations (name, code) VALUES ('Bouvet Island', 'BV');
INSERT INTO nations (name, code) VALUES ('Brazil', 'BR');
INSERT INTO nations (name, code) VALUES ('British Indian Ocean Territory', 'IO');
INSERT INTO nations (name, code) VALUES ('Brunei Darussalam', 'BN');
INSERT INTO nations (name, code) VALUES ('Bulgaria', 'BG');
INSERT INTO nations (name, code) VALUES ('Burkina Faso', 'BF');
INSERT INTO nations (name, code) VALUES ('Burundi', 'BI');
INSERT INTO nations (name, code) VALUES ('Cambodia', 'KH');
INSERT INTO nations (name, code) VALUES ('Cameroon', 'CM');
INSERT INTO nations (name, code) VALUES ('Canada', 'CA');
INSERT INTO nations (name, code) VALUES ('Cape Verde', 'CV');
INSERT INTO nations (name, code) VALUES ('Cayman Islands', 'KY');
INSERT INTO nations (name, code) VALUES ('Central African Republic', 'CF');
INSERT INTO nations (name, code) VALUES ('Chad', 'TD');
INSERT INTO nations (name, code) VALUES ('Chile', 'CL');
INSERT INTO nations (name, code) VALUES ('China', 'CN');
INSERT INTO nations (name, code) VALUES ('Christmas Island', 'CX');
INSERT INTO nations (name, code) VALUES ('Cocos (Keeling) Islands', 'CC');
INSERT INTO nations (name, code) VALUES ('Colombia', 'CO');
INSERT INTO nations (name, code) VALUES ('Comoros', 'KM');
INSERT INTO nations (name, code) VALUES ('Congo', 'CG');
INSERT INTO nations (name, code) VALUES ('Cook Islands', 'CK');
INSERT INTO nations (name, code) VALUES ('Costa Rica', 'CR');
INSERT INTO nations (name, code) VALUES ('Croatia', 'HR');
INSERT INTO nations (name, code) VALUES ('Cuba', 'CU');
INSERT INTO nations (name, code) VALUES ('Cyprus', 'CY');
INSERT INTO nations (name, code) VALUES ('Czech Republic', 'CZ');
INSERT INTO nations (name, code) VALUES ('Denmark', 'DK');
INSERT INTO nations (name, code) VALUES ('Djibouti', 'DJ');
INSERT INTO nations (name, code) VALUES ('Dominica', 'DM');
INSERT INTO nations (name, code) VALUES ('Dominican Republic', 'DO');
INSERT INTO nations (name, code) VALUES ('Ecuador', 'EC');
INSERT INTO nations (name, code) VALUES ('Egypt', 'EG');
INSERT INTO nations (name, code) VALUES ('El Salvador', 'SV');
INSERT INTO nations (name, code) VALUES ('Equatorial Guinea', 'GQ');
INSERT INTO nations (name, code) VALUES ('Eritrea', 'ER');
INSERT INTO nations (name, code) VALUES ('Estonia', 'EE');
INSERT INTO nations (name, code) VALUES ('Ethiopia', 'ET');
INSERT INTO nations (name, code) VALUES ('Falkland Islands (Malvinas)' ,'FK');
INSERT INTO nations (name, code) VALUES ('Faroe Islands', 'FO');
INSERT INTO nations (name, code) VALUES ('Fiji', 'FJ');
INSERT INTO nations (name, code) VALUES ('Finland', 'FI');
INSERT INTO nations (name, code) VALUES ('France', 'FR');
INSERT INTO nations (name, code) VALUES ('French Guiana', 'GF');
INSERT INTO nations (name, code) VALUES ('French Polynesia', 'PF');
INSERT INTO nations (name, code) VALUES ('French Southern Territories', 'TF');
INSERT INTO nations (name, code) VALUES ('Gabon', 'GA');
INSERT INTO nations (name, code) VALUES ('Gambia', 'GM');
INSERT INTO nations (name, code) VALUES ('Georgia', 'GE');
INSERT INTO nations (name, code) VALUES ('Germany', 'DE');
INSERT INTO nations (name, code) VALUES ('Ghana', 'GH');
INSERT INTO nations (name, code) VALUES ('Gibraltar', 'GI');
INSERT INTO nations (name, code) VALUES ('Greece', 'GR');
INSERT INTO nations (name, code) VALUES ('Greenland', 'GL');
INSERT INTO nations (name, code) VALUES ('Grenada', 'GD');
INSERT INTO nations (name, code) VALUES ('Guadeloupe', 'GP');
INSERT INTO nations (name, code) VALUES ('Guam', 'GU');
INSERT INTO nations (name, code) VALUES ('Guatemala', 'GT');
INSERT INTO nations (name, code) VALUES ('Guernsey', 'GG');
INSERT INTO nations (name, code) VALUES ('Guinea', 'GN');
INSERT INTO nations (name, code) VALUES ('Guinea-Bissau', 'GW');
INSERT INTO nations (name, code) VALUES ('Guyana', 'GY');
INSERT INTO nations (name, code) VALUES ('Haiti', 'HT');
INSERT INTO nations (name, code) VALUES ('Heard Island and McDonald Islands', 'HM');
INSERT INTO nations (name, code) VALUES ('Holy See (Vatican City State)' ,'VA');
INSERT INTO nations (name, code) VALUES ('Honduras', 'HN');
INSERT INTO nations (name, code) VALUES ('Hong Kong', 'HK');
INSERT INTO nations (name, code) VALUES ('Hungary', 'HU');
INSERT INTO nations (name, code) VALUES ('Iceland', 'IS');
INSERT INTO nations (name, code) VALUES ('India', 'IN');
INSERT INTO nations (name, code) VALUES ('Indonesia', 'ID');
INSERT INTO nations (name, code) VALUES ('Iraq', 'IQ');
INSERT INTO nations (name, code) VALUES ('Ireland', 'IE');
INSERT INTO nations (name, code) VALUES ('Isle of Man', 'IM');
INSERT INTO nations (name, code) VALUES ('Israel', 'IL');
INSERT INTO nations (name, code) VALUES ('Italy', 'IT');
INSERT INTO nations (name, code) VALUES ('Jamaica', 'JM');
INSERT INTO nations (name, code) VALUES ('Japan', 'JP');
INSERT INTO nations (name, code) VALUES ('Jersey', 'JE');
INSERT INTO nations (name, code) VALUES ('Jordan', 'JO');
INSERT INTO nations (name, code) VALUES ('Kazakhstan', 'KZ');
INSERT INTO nations (name, code) VALUES ('Kenya', 'KE');
INSERT INTO nations (name, code) VALUES ('Kiribati', 'KI');
INSERT INTO nations (name, code) VALUES ('Kuwait', 'KW');
INSERT INTO nations (name, code) VALUES ('Kyrgyzstan', 'KG');
INSERT INTO nations (name, code) VALUES ('Lao Peoples Democratic Republic', 'LA');
INSERT INTO nations (name, code) VALUES ('Latvia', 'LV');
INSERT INTO nations (name, code) VALUES ('Lebanon', 'LB');
INSERT INTO nations (name, code) VALUES ('Lesotho', 'LS');
INSERT INTO nations (name, code) VALUES ('Liberia', 'LR');
INSERT INTO nations (name, code) VALUES ('Libya', 'LY');
INSERT INTO nations (name, code) VALUES ('Liechtenstein', 'LI');
INSERT INTO nations (name, code) VALUES ('Lithuania', 'LT');
INSERT INTO nations (name, code) VALUES ('Luxembourg', 'LU');
INSERT INTO nations (name, code) VALUES ('Macao', 'MO');
INSERT INTO nations (name, code) VALUES ('Madagascar', 'MG');
INSERT INTO nations (name, code) VALUES ('Malawi', 'MW');
INSERT INTO nations (name, code) VALUES ('Malaysia', 'MY');
INSERT INTO nations (name, code) VALUES ('Maldives', 'MV');
INSERT INTO nations (name, code) VALUES ('Mali', 'ML');
INSERT INTO nations (name, code) VALUES ('Malta', 'MT');
INSERT INTO nations (name, code) VALUES ('Marshall Islands', 'MH');
INSERT INTO nations (name, code) VALUES ('Martinique', 'MQ');
INSERT INTO nations (name, code) VALUES ('Mauritania', 'MR');
INSERT INTO nations (name, code) VALUES ('Mauritius', 'MU');
INSERT INTO nations (name, code) VALUES ('Mayotte', 'YT');
INSERT INTO nations (name, code) VALUES ('Mexico', 'MX');
INSERT INTO nations (name, code) VALUES ('Monaco', 'MC');
INSERT INTO nations (name, code) VALUES ('Mongolia', 'MN');
INSERT INTO nations (name, code) VALUES ('Montenegro', 'ME');
INSERT INTO nations (name, code) VALUES ('Montserrat', 'MS');
INSERT INTO nations (name, code) VALUES ('Morocco', 'MA');
INSERT INTO nations (name, code) VALUES ('Mozambique', 'MZ');
INSERT INTO nations (name, code) VALUES ('Myanmar', 'MM');
INSERT INTO nations (name, code) VALUES ('Namibia', 'NA');
INSERT INTO nations (name, code) VALUES ('Nauru', 'NR');
INSERT INTO nations (name, code) VALUES ('Nepal', 'NP');
INSERT INTO nations (name, code) VALUES ('Netherlands', 'NL');
INSERT INTO nations (name, code) VALUES ('New Caledonia', 'NC');
INSERT INTO nations (name, code) VALUES ('New Zealand', 'NZ');
INSERT INTO nations (name, code) VALUES ('Nicaragua', 'NI');
INSERT INTO nations (name, code) VALUES ('Niger', 'NE');
INSERT INTO nations (name, code) VALUES ('Nigeria', 'NG');
INSERT INTO nations (name, code) VALUES ('Niue', 'NU');
INSERT INTO nations (name, code) VALUES ('Norfolk Island', 'NF');
INSERT INTO nations (name, code) VALUES ('Northern Mariana Islands', 'MP');
INSERT INTO nations (name, code) VALUES ('Norway', 'NO');
INSERT INTO nations (name, code) VALUES ('Oman', 'OM');
INSERT INTO nations (name, code) VALUES ('Pakistan', 'PK');
INSERT INTO nations (name, code) VALUES ('Palau', 'PW');
INSERT INTO nations (name, code) VALUES ('Panama', 'PA');
INSERT INTO nations (name, code) VALUES ('Papua New Guinea', 'PG');
INSERT INTO nations (name, code) VALUES ('Paraguay', 'PY');
INSERT INTO nations (name, code) VALUES ('Peru', 'PE');
INSERT INTO nations (name, code) VALUES ('Philippines', 'PH');
INSERT INTO nations (name, code) VALUES ('Pitcairn', 'PN');
INSERT INTO nations (name, code) VALUES ('Poland', 'PL');
INSERT INTO nations (name, code) VALUES ('Portugal', 'PT');
INSERT INTO nations (name, code) VALUES ('Puerto Rico', 'PR');
INSERT INTO nations (name, code) VALUES ('Qatar', 'QA');
INSERT INTO nations (name, code) VALUES ('Romania', 'RO');
INSERT INTO nations (name, code) VALUES ('Russian Federation', 'RU');
INSERT INTO nations (name, code) VALUES ('Rwanda', 'RW');
INSERT INTO nations (name, code) VALUES ('Saint Kitts and Nevis', 'KN');
INSERT INTO nations (name, code) VALUES ('Saint Lucia', 'LC');
INSERT INTO nations (name, code) VALUES ('Saint Martin (French part)' ,'MF');
INSERT INTO nations (name, code) VALUES ('Saint Pierre and Miquelon', 'PM');
INSERT INTO nations (name, code) VALUES ('Saint Vincent and the Grenadines', 'VC');
INSERT INTO nations (name, code) VALUES ('Samoa', 'WS');
INSERT INTO nations (name, code) VALUES ('San Marino', 'SM');
INSERT INTO nations (name, code) VALUES ('Sao Tome and Principe', 'ST');
INSERT INTO nations (name, code) VALUES ('Saudi Arabia', 'SA');
INSERT INTO nations (name, code) VALUES ('Senegal', 'SN');
INSERT INTO nations (name, code) VALUES ('Serbia', 'RS');
INSERT INTO nations (name, code) VALUES ('Seychelles', 'SC');
INSERT INTO nations (name, code) VALUES ('Sierra Leone', 'SL');
INSERT INTO nations (name, code) VALUES ('Singapore', 'SG');
INSERT INTO nations (name, code) VALUES ('Sint Maarten (Dutch part)' ,'SX');
INSERT INTO nations (name, code) VALUES ('Slovakia', 'SK');
INSERT INTO nations (name, code) VALUES ('Slovenia', 'SI');
INSERT INTO nations (name, code) VALUES ('Solomon Islands', 'SB');
INSERT INTO nations (name, code) VALUES ('Somalia', 'SO');
INSERT INTO nations (name, code) VALUES ('South Africa', 'ZA');
INSERT INTO nations (name, code) VALUES ('South Georgia and the South Sandwich Islands', 'GS');
INSERT INTO nations (name, code) VALUES ('South Sudan', 'SS');
INSERT INTO nations (name, code) VALUES ('Spain', 'ES');
INSERT INTO nations (name, code) VALUES ('Sri Lanka', 'LK');
INSERT INTO nations (name, code) VALUES ('Sudan', 'SD');
INSERT INTO nations (name, code) VALUES ('Suriname', 'SR');
INSERT INTO nations (name, code) VALUES ('Svalbard and Jan Mayen', 'SJ');
INSERT INTO nations (name, code) VALUES ('Swaziland', 'SZ');
INSERT INTO nations (name, code) VALUES ('Sweden', 'SE');
INSERT INTO nations (name, code) VALUES ('Switzerland', 'CH');
INSERT INTO nations (name, code) VALUES ('Syrian Arab Republic', 'SY');
INSERT INTO nations (name, code) VALUES ('Tajikistan', 'TJ');
INSERT INTO nations (name, code) VALUES ('Thailand', 'TH');
INSERT INTO nations (name, code) VALUES ('Timor-Leste', 'TL');
INSERT INTO nations (name, code) VALUES ('Togo', 'TG');
INSERT INTO nations (name, code) VALUES ('Tokelau', 'TK');
INSERT INTO nations (name, code) VALUES ('Tonga', 'TO');
INSERT INTO nations (name, code) VALUES ('Trinidad and Tobago', 'TT');
INSERT INTO nations (name, code) VALUES ('Tunisia', 'TN');
INSERT INTO nations (name, code) VALUES ('Turkey', 'TR');
INSERT INTO nations (name, code) VALUES ('Turkmenistan', 'TM');
INSERT INTO nations (name, code) VALUES ('Turks and Caicos Islands', 'TC');
INSERT INTO nations (name, code) VALUES ('Tuvalu', 'TV');
INSERT INTO nations (name, code) VALUES ('Uganda', 'UG');
INSERT INTO nations (name, code) VALUES ('Ukraine', 'UA');
INSERT INTO nations (name, code) VALUES ('United Arab Emirates', 'AE');
INSERT INTO nations (name, code) VALUES ('United Kingdom', 'GB');
INSERT INTO nations (name, code) VALUES ('United States', 'US');
INSERT INTO nations (name, code) VALUES ('United States Minor Outlying Islands', 'UM');
INSERT INTO nations (name, code) VALUES ('Uruguay', 'UY');
INSERT INTO nations (name, code) VALUES ('Uzbekistan', 'UZ');
INSERT INTO nations (name, code) VALUES ('Vanuatu', 'VU');
INSERT INTO nations (name, code) VALUES ('Viet Nam', 'VN');
INSERT INTO nations (name, code) VALUES ('Wallis and Futuna', 'WF');
INSERT INTO nations (name, code) VALUES ('Western Sahara', 'EH');
INSERT INTO nations (name, code) VALUES ('Yemen', 'YE');
INSERT INTO nations (name, code) VALUES ('Zambia', 'ZM');
INSERT INTO nations (name, code) VALUES ('Zimbabwe', 'ZW');

-- End nations initialization

-- Roles initialization

INSERT INTO roles VALUES (NULL, 'ADMIN');
INSERT INTO roles VALUES (NULL, 'CUSTOMER');

-- Users initialization
INSERT INTO users VALUES (NULL, 'Gerard','admin@admin.com', '$2y$10$HCzABbNv9skrPq2C1TncOeNhC2JQ0lFdq0UyfVeYlTzYDHRAQ2ZnC', 'Casas', 'Serarols', 0, date '2002-10-03', NULL, 'M', 1 ,193, NULL, NULL ,now(), now());


