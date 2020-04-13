DELIMITER $$
--
-- Процедуры
--
CREATE PROCEDURE `RENT_BOOK` (IN `in_book_id` INT UNSIGNED, IN `in_client_id` INT UNSIGNED, INOUT `out_rented_book_copy_id` INT UNSIGNED)  NO SQL
BEGIN
SELECT `id` INTO out_rented_book_copy_id FROM `book_list` WHERE `book_id` = in_book_id AND `available` = 1 LIMIT 1;
INSERT INTO `log_lease`(`id`, `book_list_id`, `client_id`, `date_create`, `date_returned`) VALUES (NULL,out_rented_book_copy_id,in_client_id,UNIX_TIMESTAMP(),NULL);
UPDATE `book_list` SET `book_list`.`available`=0 WHERE `book_list`.`id` = out_rented_book_copy_id;
END$$

CREATE PROCEDURE `RETURN_RENT_BOOK` (IN `in_rent_id` INT UNSIGNED)  NO SQL
BEGIN
DECLARE day_overdue_count, fine_per_day, fine INT;
DECLARE client_id, book_copy_id INT UNSIGNED;
SELECT `category`.`fine_per_day`, `log_lease`.`client_id`, `log_lease`.`book_list_id` INTO fine_per_day, client_id, book_copy_id FROM `log_lease` LEFT JOIN `book_list` ON `book_list`.`id` = `log_lease`.`book_list_id` LEFT JOIN `book` ON `book`.`id` = `book_list`.`book_id` LEFT JOIN `category` ON `category`.`id` = `book`.`category_id` WHERE `log_lease`.`id` = in_rent_id AND `log_lease`.`date_returned` IS NULL;
SELECT `GET_DAY_RENT_OVERDUE`(in_rent_id) INTO day_overdue_count;
IF day_overdue_count != 0 THEN 
  IF client_id IS NOT NULL THEN
    SET fine = -(day_overdue_count*fine_per_day);
    INSERT INTO `log_debt`(`id`, `user_id`, `amount`, `date`) VALUES (NULL,client_id,fine,UNIX_TIMESTAMP());
  END IF;
END IF;
UPDATE `log_lease` SET `date_returned`=UNIX_TIMESTAMP() WHERE `id`=in_rent_id;
UPDATE `book_list` SET `available`=1 WHERE `id`=book_copy_id;
END$$

--
-- Функции
--
CREATE FUNCTION `GET_DAY_RENT_OVERDUE` (`in_rent_id` INT) RETURNS INT(11) NO SQL
BEGIN
DECLARE day_count INT;
SELECT
  CAST( ( ( UNIX_TIMESTAMP() - ( `log_lease`.`date_create` + ( `category`.`expiration_day` * 86400 ) ) ) / 86400 + 1 ) AS INT ) INTO day_count
FROM
  `log_lease`
LEFT JOIN
  `book_list` ON `book_list`.`id` = `log_lease`.`book_list_id`
LEFT JOIN
  `book` ON `book`.`id` = `book_list`.`book_id`
LEFT JOIN
  `category` ON `category`.`id` = `book`.`category_id`
WHERE
  `log_lease`.`id` = in_rent_id AND `log_lease`.`date_returned` IS NULL AND ( `log_lease`.`date_create` + `category`.`expiration_day` * 86400 < UNIX_TIMESTAMP() );
IF day_count IS NULL THEN 
  RETURN 0;
ELSE
  RETURN day_count;
END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `author`
--

CREATE TABLE `author` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(200) NOT NULL,
  `description` text
);


-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(200) NOT NULL,
  `expiration_day` int(10) UNSIGNED NOT NULL,
  `fine_per_day` int(10) UNSIGNED NOT NULL
);


-- --------------------------------------------------------

--
-- Структура таблицы `publisher`
--

CREATE TABLE `publisher` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(200) NOT NULL,
  `description` text
);

-- --------------------------------------------------------

--
-- Структура таблицы `book`
--

CREATE TABLE `book` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `publisher_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(300) NOT NULL,
  `description` text,
  `isbn` int(11) NOT NULL UNIQUE,
  `year` smallint(6) NOT NULL,
   FOREIGN KEY (`publisher_id`) REFERENCES `publisher` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
   FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON UPDATE CASCADE
);

-- --------------------------------------------------------

--
-- Структура таблицы `book_authors`
--

CREATE TABLE `book_authors` (
  `book_id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- --------------------------------------------------------

--
-- Структура таблицы `book_list`
--

CREATE TABLE `book_list` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `book_id` int(10) UNSIGNED NOT NULL,
  `place` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL,
  FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- --------------------------------------------------------

--
-- Структура таблицы `global_user_account`
--

CREATE TABLE `global_user_account` (
  `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_login` varchar(32) NOT NULL UNIQUE,
  `user_pass` char(40) DEFAULT NULL,
  `user_registration` int(10) UNSIGNED NOT NULL,
  `user_real_name` varchar(100) DEFAULT NULL
);

--
-- Дамп данных таблицы `global_user_account`
--

INSERT INTO `global_user_account` (`user_login`, `user_pass`, `user_registration`, `user_real_name`) VALUES
('admin', '6905a5a04431263d1a2b99a95729e650d4e41b4a', UNIX_TIMESTAMP(), 'Админ Админыч');

-- --------------------------------------------------------

--
-- Структура таблицы `global_session`
--

CREATE TABLE `global_session` (
  `gbl_sid` char(32) NOT NULL PRIMARY KEY,
  `user_id` int(10) UNSIGNED NOT NULL,
  `gbl_session_create` int(10) UNSIGNED NOT NULL,
  `gbl_session_last_time` int(10) UNSIGNED NOT NULL,
  `gbl_session_last_ip` char(24) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `global_user_account` (`user_id`)
);

-- --------------------------------------------------------

--
-- Структура таблицы `global_user_group`
--

CREATE TABLE `global_user_group` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(32) NOT NULL,
  `group_expires` int(10) UNSIGNED DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `global_user_account` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Дамп данных таблицы `global_user_group`
--

INSERT INTO `global_user_group` (`user_id`, `group_name`, `group_expires`) VALUES
(1, 'root', NULL),
(1, 'user', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `lb_session`
--

CREATE TABLE `lb_session` (
  `sid` char(32) NOT NULL PRIMARY KEY,
  `gbl_sid` char(32) NOT NULL,
  `session_create` int(10) UNSIGNED NOT NULL,
  `session_status` tinyint(3) NOT NULL,
  `user_hash` char(32) NOT NULL,
  `user_csrf` char(32) NOT NULL,
  FOREIGN KEY (`gbl_sid`) REFERENCES `global_session` (`gbl_sid`)
);


-- --------------------------------------------------------

--
-- Структура таблицы `user_balance`
--

CREATE TABLE `user_balance` (
  `user_id` int(10) UNSIGNED NOT NULL UNIQUE,
  `amount` int(11) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `global_user_account` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- --------------------------------------------------------

--
-- Структура таблицы `log_debt`
--

CREATE TABLE `log_debt` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `global_user_account` (`user_id`)
);

--
-- Триггеры `log_debt`
--
DELIMITER $$
CREATE TRIGGER `CHANGE_BALANCE` AFTER INSERT ON `log_debt` FOR EACH ROW UPDATE `user_balance` SET `user_balance`.`amount`=`user_balance`.`amount`+NEW.`amount` WHERE `user_id` = NEW.`user_id`
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `log_lease`
--

CREATE TABLE `log_lease` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `book_list_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `date_create` int(10) UNSIGNED NOT NULL,
  `date_returned` int(10) UNSIGNED DEFAULT NULL,
  FOREIGN KEY (`book_list_id`) REFERENCES `book_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `global_user_account` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
