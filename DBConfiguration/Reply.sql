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
-- テーブルの構造 `Reply`
--

CREATE TABLE `Reply` (
  `userID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `questionID` int NOT NULL,
  `replyID` int NOT NULL,
  `parentID` int NOT NULL,
  `parentType` enum('a','r') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `replyText` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `appendFile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `Reply`
--

--
-- トリガ `Reply`
--
DELIMITER $$
CREATE TRIGGER `before_Reply_count_insert` BEFORE INSERT ON `Reply` FOR EACH ROW BEGIN
    DECLARE next_replyID INT;

    IF NEW.parentType = 'a' THEN
        -- Answerテーブルを参照
        IF NOT EXISTS (SELECT answerID FROM Answer WHERE answerID = NEW.parentID) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'parentID does not exist in existing answerID';
        ELSE
            -- 新しいレコードの親が存在しない場合
            IF NOT EXISTS (SELECT replyID FROM Reply WHERE questionID = NEW.questionID AND parentType = NEW.parentType AND parentID = NEW.parentID) THEN
                SET next_replyID = 0;
            ELSE
                -- 存在する場合、次のreplyIDを設定
                SET next_replyID = (SELECT COALESCE(MAX(replyID), 0) + 1 FROM Reply WHERE questionID = NEW.questionID AND parentType = NEW.parentType AND parentID = NEW.parentID);
            END IF;

            SET NEW.replyID = next_replyID;
        END IF;

    ELSEIF NEW.parentType = 'r' THEN
        -- Replyテーブルを参照
        IF NOT EXISTS (SELECT replyID FROM Reply WHERE replyID = NEW.parentID) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'parentID does not exist in existing replyID';
        ELSE
            -- 新しいレコードの親が存在しない場合
            IF NOT EXISTS (SELECT replyID FROM Reply WHERE questionID = NEW.questionID AND parentType = NEW.parentType AND parentID = NEW.parentID) THEN
                SET next_replyID = 0;
            ELSE
                -- 存在する場合、次のreplyIDを設定
                SET next_replyID = (SELECT COALESCE(MAX(replyID), 0) + 1 FROM Reply WHERE questionID = NEW.questionID AND parentType = NEW.parentType AND parentID = NEW.parentID);
            END IF;

            SET NEW.replyID = next_replyID;
        END IF;
    END IF;
END
$$
DELIMITER ;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `Reply`
--
ALTER TABLE `Reply`
  ADD PRIMARY KEY (`userID`,`questionID`,`replyID`,`parentID`,`parentType`),
  ADD KEY `Reply_ibfk_2` (`questionID`),
  ADD KEY `Reply_ibfk_3` (`parentID`);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `Reply`
--
ALTER TABLE `Reply`
  ADD CONSTRAINT `Reply_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`),
  ADD CONSTRAINT `Reply_ibfk_2` FOREIGN KEY (`questionID`) REFERENCES `Question` (`questionID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
