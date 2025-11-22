-- グループ機能用テーブル
CREATE TABLE `GroupRooms` (
  `groupID` int NOT NULL AUTO_INCREMENT,
  `groupName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `ownerID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`groupID`),
  KEY `ownerID` (`ownerID`),
  CONSTRAINT `GroupRooms_ibfk_1` FOREIGN KEY (`ownerID`) REFERENCES `Users` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `GroupMembers` (
  `groupID` int NOT NULL,
  `userID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'member',
  `joinedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`),
  CONSTRAINT `GroupMembers_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `GroupRooms` (`groupID`) ON DELETE CASCADE,
  CONSTRAINT `GroupMembers_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `GroupMessages` (
  `messageID` int NOT NULL AUTO_INCREMENT,
  `groupID` int NOT NULL,
  `userID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `commentText` text COLLATE utf8mb4_general_ci,
  `appendFile` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`messageID`),
  KEY `groupID` (`groupID`),
  KEY `userID` (`userID`),
  CONSTRAINT `GroupMessages_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `GroupRooms` (`groupID`) ON DELETE CASCADE,
  CONSTRAINT `GroupMessages_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
