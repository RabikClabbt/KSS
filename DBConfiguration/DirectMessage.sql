-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql305.phy.lolipop.lan
-- 生成日時: 2024 年 7 月 23 日 01:48
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
-- テーブルの構造 `DirectMessage`
--

CREATE TABLE `DirectMessage` (
  `userID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `partnerID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `commentID` int NOT NULL,
  `commentText` text COLLATE utf8mb4_general_ci,
  `appendFile` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `DirectMessage`
--

--
-- トリガ `DirectMessage`
--
DELIMITER $$
CREATE TRIGGER `before_insert_direct_message` BEFORE INSERT ON `DirectMessage` FOR EACH ROW BEGIN
    DECLARE max_comment_id INT;
    
    -- 現在の会話の最大commentIDを取得
    SELECT COALESCE(MAX(commentID), -1) INTO max_comment_id
    FROM DirectMessage
    WHERE (userID = NEW.userID AND partnerID = NEW.partnerID)
       OR (userID = NEW.partnerID AND partnerID = NEW.userID);
    
    -- 新しいcommentIDを設定
    SET NEW.commentID = max_comment_id + 1;
END
$$
DELIMITER ;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `DirectMessage`
--
ALTER TABLE `DirectMessage`
  ADD PRIMARY KEY (`userID`,`partnerID`,`commentID`),
  ADD KEY `partnerID` (`partnerID`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `DirectMessage`
--
ALTER TABLE `DirectMessage`
  ADD CONSTRAINT `DirectMessage_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`),
  ADD CONSTRAINT `DirectMessage_ibfk_2` FOREIGN KEY (`partnerID`) REFERENCES `Users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
