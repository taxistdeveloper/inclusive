-- Выполните в phpMyAdmin для существующей БД inclusive (если таблица ещё не создана).

USE inclusive;

CREATE TABLE IF NOT EXISTS documents (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  section VARCHAR(32) NOT NULL COMMENT 'family|rules|society|career|college|tech',
  title VARCHAR(500) NOT NULL,
  pdf_path VARCHAR(1024) NOT NULL,
  icon_class VARCHAR(128) NOT NULL DEFAULT 'bi-file-earmark-text-fill',
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_documents_section_sort (section, sort_order, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Начальные данные (как на статической главной). Пропустит дубли при повторном запуске, если нужно — очистите таблицу вручную.
INSERT INTO documents (section, title, pdf_path, icon_class, sort_order) VALUES
('family', 'Регулярное общение с педагогами и специалистами', 'pdf/communication.pdf', 'bi-chat-dots-fill', 1),
('family', 'Участие в разработке индивидуального образовательного плана', 'pdf/education-plan.pdf', 'bi-file-earmark-text-fill', 2),
('family', 'Поддержка обучения дома', 'pdf/family.pdf', 'bi-house-heart-fill', 3),
('family', 'Участие в мероприятиях колледжа', 'pdf/events.pdf', 'bi-calendar-event-fill', 4),
('family', 'Обмен опытом с другими семьями', 'pdf/experience.pdf', 'bi-people-fill', 5),
('rules', 'Главные ориентиры и правила', 'pdf/2. Главные ориентиры и правила.pdf', 'bi-file-earmark-text-fill', 1),
('rules', 'Закон о правах ребенка', 'pdf/Закон о правах ребенка _.01-07-2025.rus.pdf', 'bi-journal-bookmark-fill', 2),
('rules', 'Концепция разв обрз', 'pdf/Концепция разв обрз .25-04-2025.rus.pdf', 'bi-list-check', 3),
('rules', 'Концепция развития ИО', 'pdf/Концепция развития ИО .30-12-2024.rus.pdf', 'bi-list-check', 4),
('rules', 'Приказ №4', 'pdf/Приказ №4 .06-05-2025.rus.pdf', 'bi-list-check', 5),
('rules', 'Приказ №92', 'pdf/Приказ №92  от.29-04-2025.rus.pdf', 'bi-list-check', 6);
