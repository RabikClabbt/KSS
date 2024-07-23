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
-- テーブルの構造 `GlobalChat`
--

CREATE TABLE `GlobalChat` (
  `userID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Anonymous',
  `commentID` int NOT NULL,
  `replyID` int DEFAULT NULL,
  `commentText` text COLLATE utf8mb4_general_ci,
  `appendFile` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `GlobalChat`
--

--
-- トリガ `GlobalChat`
--
DELIMITER $$
CREATE TRIGGER `before_insert_GlobalChat` BEFORE INSERT ON `GlobalChat` FOR EACH ROW BEGIN
    IF NEW.commentID = NEW.replyID THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'commentID and replyID cannot be the same';
    END IF;
END
$$
DELIMITER ;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `GlobalChat`
--
ALTER TABLE `GlobalChat`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `userID` (`userID`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `GlobalChat`
--
ALTER TABLE `GlobalChat`
  MODIFY `commentID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `GlobalChat`
--
ALTER TABLE `GlobalChat`
  ADD CONSTRAINT `GlobalChat_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
