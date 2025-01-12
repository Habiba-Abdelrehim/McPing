SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mcping`
--

-- --------------------------------------------------------

--
-- Table structure for table `Boards`
--

CREATE TABLE `Boards` (
  `board` text NOT NULL,
  `invite_id` char(8) NOT NULL,
  `owner` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Memberships`
--

CREATE TABLE `Memberships` (
  `email` text NOT NULL,
  `board` text NOT NULL,
  `role` enum('user','owner') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Private_Memberships`
--

CREATE TABLE `Private_Memberships` (
  `email` text NOT NULL,
  `board` text NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Private_Messages`
--

CREATE TABLE `Private_Messages` (
  `board` text NOT NULL,
  `owner` text NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `email` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `studentorstaffid` int(9) NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `type` enum('Student','TA','Professor') NOT NULL,
  `token` text DEFAULT NULL,
  `token_expiry` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Boards`
--
ALTER TABLE `Boards`
  ADD UNIQUE KEY `board` (`board`) USING HASH;

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD UNIQUE KEY `email` (`email`) USING HASH;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Table structure for table `Invitations`
--

CREATE TABLE `Invitations` (
  `board` text NOT NULL,
  `email` text NOT NULL,
  `invite_code` char(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Invitations`
--

INSERT INTO `Invitations` (`board`, `email`, `invite_code`) VALUES
('Math 323', 'invited.user@mail.com', 'abc123'),
('Comp 445', 'another.user@mail.com', 'def456');



--
-- Table structure for table `Channels`
--

CREATE TABLE `Channels` (
  `board` text NOT NULL,
  `channel_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Channels`
--

INSERT INTO `Channels` (`board`, `channel_name`) VALUES
('Math 323', 'General'),
('Math 323', 'Homework'),
('Comp 445', 'Announcements'),
('Comp 315', 'Discussion');



-- Table structure for table Channel `Messages`
CREATE TABLE `Messages` (
  `board` text NOT NULL,
  `channel` text NOT NULL,
  `user` text NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `Messages`
INSERT INTO `Messages` (`board`, `channel`, `user`, `message`) VALUES
('Math 323', 'General', 'User1', 'Hello, Math 323!'),
('Math 323', 'Homework', 'User2', 'Dont forget to submit your assignments.'),
('Comp 445', 'Announcements', 'User3', 'Important announcement!'),
('Comp 315', 'Discussion', 'User4', 'Lets discuss the project.');

