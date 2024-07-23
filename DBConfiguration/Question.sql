-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql305.phy.lolipop.lan
-- 生成日時: 2024 年 7 月 23 日 01:49
-- サーバのバージョン： 8.0.35
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `LAA1517467-yadix`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `Question`
--

CREATE TABLE `Question` (
  `userID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` int DEFAULT NULL,
  `questionID` int NOT NULL,
  `questionTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `questionText` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `appendFile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `Question`
--

--
-- トリガ `Question`
--
DELIMITER $$
CREATE TRIGGER `before_Question_insert` BEFORE INSERT ON `Question` FOR EACH ROW BEGIN
    DECLARE next_questionID INT;
    
    IF (SELECT COUNT(*) FROM Question) = 0 THEN
        SET next_questionID = 0;
    ELSE
        SET next_questionID = (SELECT MAX(questionID) + 1 FROM Question);
    END IF;
    
    SET NEW.questionID = next_questionID;
END
$$
DELIMITER ;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `Question`
--
ALTER TABLE `Question`
  ADD PRIMARY KEY (`userID`,`questionID`),
  ADD KEY `idx_questionID` (`questionID`),
  ADD KEY `fk_category` (`category`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `Question`
--
ALTER TABLE `Question`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category`) REFERENCES `Category` (`categoryID`),
  ADD CONSTRAINT `Question_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
