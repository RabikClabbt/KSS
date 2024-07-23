-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql305.phy.lolipop.lan
-- 生成日時: 2024 年 7 月 23 日 01:47
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
-- テーブルの構造 `Answer`
--

CREATE TABLE `Answer` (
  `userID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `questionID` int NOT NULL,
  `answerID` int NOT NULL,
  `bestFlg` int DEFAULT '0',
  `answerText` text COLLATE utf8mb4_general_ci,
  `appendFile` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `Answer`
--

--
-- トリガ `Answer`
--
DELIMITER $$
CREATE TRIGGER `before_Answer_insert` BEFORE INSERT ON `Answer` FOR EACH ROW BEGIN
    DECLARE next_answerID INT;
    
    IF NOT EXISTS (SELECT * FROM Answer WHERE questionID = NEW.questionID) THEN
        SET next_answerID = 0;
    ELSE
        SET next_answerID = (SELECT MAX(answerID) + 1 FROM Answer WHERE questionID = NEW.questionID);
    END IF;
    
    SET NEW.answerID = next_answerID;
END
$$
DELIMITER ;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `Answer`
--
ALTER TABLE `Answer`
  ADD PRIMARY KEY (`userID`,`questionID`,`answerID`),
  ADD KEY `questionID` (`questionID`),
  ADD KEY `idx_answerID` (`answerID`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `Answer`
--
ALTER TABLE `Answer`
  ADD CONSTRAINT `Answer_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`),
  ADD CONSTRAINT `Answer_ibfk_2` FOREIGN KEY (`questionID`) REFERENCES `Question` (`questionID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
