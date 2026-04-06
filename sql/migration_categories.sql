-- Выполните в phpMyAdmin для существующей БД inclusive.

USE inclusive;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(32) NOT NULL COMMENT 'ключ раздела в documents.section',
  title VARCHAR(255) NOT NULL,
  subtitle VARCHAR(500) NOT NULL DEFAULT '',
  icon_class VARCHAR(128) NOT NULL DEFAULT 'bi-circle-fill',
  pill_label VARCHAR(128) NOT NULL DEFAULT 'Материалы',
  badge_class VARCHAR(128) NOT NULL DEFAULT 'bg-primary',
  badge_style VARCHAR(255) NULL,
  alert_strong VARCHAR(128) NOT NULL DEFAULT 'Важно:',
  alert_text VARCHAR(1000) NOT NULL DEFAULT '',
  modal_id VARCHAR(64) NOT NULL COMMENT 'id модального окна Bootstrap',
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  admin_icon_wrap_class VARCHAR(255) NOT NULL DEFAULT 'bg-primary bg-opacity-10 text-primary',
  admin_border_class VARCHAR(255) NOT NULL DEFAULT 'border border-primary border-opacity-25',
  admin_icon_wrap_style VARCHAR(255) NULL,
  admin_border_style VARCHAR(255) NULL,
  diagram_icon_class VARCHAR(64) NOT NULL DEFAULT 'text-primary' COMMENT 'цвет иконки на главной',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug),
  UNIQUE KEY uq_categories_modal_id (modal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (
  slug, title, subtitle, icon_class, pill_label, badge_class, badge_style,
  alert_strong, alert_text, modal_id, sort_order,
  admin_icon_wrap_class, admin_border_class, admin_icon_wrap_style, admin_border_style,
  diagram_icon_class
) VALUES
('family', 'Семья и родители', 'Поддержка и участие семьи в образовательном процессе', 'bi-people-fill', 'Ключевые компоненты', 'bg-primary', NULL,
 'Важно:', ' Все компоненты экосистемы взаимосвязаны и работают совместно для создания инклюзивной образовательной среды', 'exampleModal', 1,
 'bg-primary bg-opacity-10 text-primary', 'border border-primary border-opacity-25', NULL, NULL,
 'text-primary'),
('rules', 'Главные ориентиры и правила', 'Нормативная база и принципы инклюзивного образования', 'bi-bullseye', 'Ключевые документы', 'text-white', 'background-color: #7209b7;',
 'Примечание:', ' Ознакомление с нормативной базой помогает обеспечить единые стандарты и качество инклюзивного образования.', 'rulesModal', 2,
 'text-white', '', 'background-color: #7209b7;', 'border-color: #7209b7 !important;',
 'text-purple'),
('society', 'Общество и сообщества', 'Социальная интеграция и взаимодействие', 'bi-globe', 'Материалы', 'bg-danger', NULL,
 'Важно:', ' Включённость в общество и сотрудничество с партнёрами усиливают поддержку обучающихся.', 'societyModal', 3,
 'bg-danger bg-opacity-10 text-danger', 'border border-danger border-opacity-25', NULL, NULL,
 'text-danger'),
('career', 'Карьерное становление', 'Подготовка к профессиональной деятельности', 'bi-briefcase-fill', 'Материалы', 'bg-success', NULL,
 'Совет:', ' Профориентация и практика помогают выбрать путь с учётом интересов и возможностей.', 'careerModal', 4,
 'bg-success bg-opacity-10 text-success', 'border border-success border-opacity-25', NULL, NULL,
 'text-success'),
('college', 'Внутриколледжная среда', 'Адаптированная образовательная среда учреждения', 'bi-building-fill', 'Материалы', 'bg-success', NULL,
 'Среда:', ' Доступная среда, наставничество и вовлечённость персонала поддерживают успех каждого студента.', 'collegeModal', 5,
 'bg-success bg-opacity-10 text-success', 'border border-success border-opacity-25', NULL, NULL,
 'text-success'),
('tech', 'Технологии и цифровые ресурсы', 'Современные инструменты для эффективного обучения', 'bi-display', 'Материалы', 'bg-warning text-dark', NULL,
 'Цифра:', ' Цифровые ресурсы повышают доступность учебных материалов и вариативность форматов работы.', 'techModal', 6,
 'bg-warning bg-opacity-25 text-dark', 'border border-warning', NULL, NULL,
 'text-warning')
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Если таблица categories уже была без поля diagram_icon_class:
-- ALTER TABLE categories ADD COLUMN diagram_icon_class VARCHAR(64) NOT NULL DEFAULT 'text-primary' COMMENT 'цвет иконки на главной' AFTER admin_border_style;
