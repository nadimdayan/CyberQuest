-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2026 at 07:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cyberquest`
--

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `level` int(11) DEFAULT 1,
  `question` text DEFAULT NULL,
  `option1` varchar(255) DEFAULT NULL,
  `option2` varchar(255) DEFAULT NULL,
  `option3` varchar(255) DEFAULT NULL,
  `option4` varchar(255) DEFAULT NULL,
  `correct_option` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `level`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_option`) VALUES
(1, 1, 'What does \"phishing\" mean?', 'Catching fish online', 'Tricking users to steal personal info', 'A type of computer virus', 'Hacking into a website', 2),
(2, 1, 'Which of these is the strongest password?', '123456', 'yourname2024', 'P@ssw0rd!#99', 'password', 3),
(3, 1, 'What does HTTPS mean in a website URL?', 'High Tech Transfer Protocol System', 'Hyper Text Transfer Protocol Secure', 'Hyper Transfer Text Protocol System', 'High Transfer Technology Protocol', 2),
(4, 1, 'What is malware?', 'Helpful software', 'Malicious software designed to harm your device', 'A type of firewall', 'An antivirus tool', 2),
(5, 1, 'What should you do with a suspicious email?', 'Open all attachments immediately', 'Reply with your personal info', 'Delete it or report it as spam', 'Forward it to all your friends', 3),
(6, 1, 'What is two-factor authentication (2FA)?', 'Logging in with two passwords', 'A second layer of verification after your password', 'A type of firewall protection', 'A VPN service', 2),
(7, 1, 'What is a VPN used for?', 'Playing games faster', 'Encrypting and hiding your internet activity', 'Making your internet faster', 'Blocking pop-up ads', 2),
(8, 1, 'What does a firewall do?', 'Speeds up internet connection', 'Monitors and blocks unauthorized network access', 'Removes viruses from files', 'Saves your passwords safely', 2),
(9, 1, 'Which of these is safest to do on public WiFi?', 'Online banking', 'Shopping with credit card', 'Browsing a public news website', 'Entering your email password', 3),
(10, 1, 'What is ransomware?', 'Software that speeds up your PC', 'Malware that locks your files and demands money', 'A free antivirus tool', 'A tool to recover deleted files', 2),
(11, 2, 'What is social engineering in cybersecurity?', 'Building social media platforms', 'Manipulating people psychologically to reveal confidential info', 'Engineering social networking apps', 'A type of encryption', 2),
(12, 2, 'What does SQL injection attack do?', 'Speeds up database queries', 'Injects malicious code into database input fields to steal data', 'Protects web forms', 'Creates new databases automatically', 2),
(13, 2, 'What is a zero-day vulnerability?', 'A very old known bug', 'An unknown security flaw with no official patch yet', 'A virus that activates on day zero', 'A daily virus scan process', 2),
(14, 2, 'What is end-to-end encryption?', 'Only the sender can read the data', 'Only admins can read the data', 'Only the sender and receiver can read the data — nobody else', 'Data is stored but not encrypted', 3),
(15, 2, 'What is a brute force attack?', 'Physically destroying computer hardware', 'Automatically trying thousands of password combinations', 'A DDoS flooding attack', 'Stealing data through social engineering', 2),
(16, 2, 'What is a keylogger?', 'A program that speeds up typing', 'Software that secretly records every keystroke you make', 'A keyboard shortcut manager', 'A tool that fixes keyboard errors', 2),
(17, 2, 'What does DDoS stand for?', 'Direct Denial of System', 'Distributed Denial of Service attack', 'Double Data over System', 'Direct Data of Service', 2),
(18, 2, 'What is a man-in-the-middle (MITM) attack?', 'Two hackers working together', 'An attacker secretly intercepts communication between two parties', 'A type of virus', 'A firewall attack', 2),
(19, 2, 'What is spyware?', 'Legal monitoring software for employees', 'Software that secretly collects your data without your knowledge', 'A tool to protect your privacy', 'An antivirus program', 2),
(20, 2, 'What is a botnet?', 'A network of secure servers', 'A group of infected computers controlled remotely by a hacker', 'A type of firewall network', 'A robot-based internet service', 2),
(21, 3, 'What is the best practice for creating passwords?', 'Use the same password for all accounts', 'Use a unique, complex password for every account', 'Use your birthday as a password', 'Use short simple passwords that are easy to remember', 2),
(22, 3, 'How often should you update your software and OS?', 'Never — updates slow down performance', 'Only when something breaks', 'Regularly and as soon as updates are available', 'Once every few years', 3),
(23, 3, 'What is a digital certificate used for?', 'Certifying you completed a course', 'Verifying the identity of a website or person online', 'Encrypting your hard drive', 'Creating a secure password', 2),
(24, 3, 'What does the padlock icon in a browser URL bar mean?', 'The website is completely safe from all threats', 'The connection between your browser and the site is encrypted', 'The site has been verified by the government', 'The site does not collect any data', 2),
(25, 3, 'What is the principle of least privilege?', 'Give all users maximum access rights', 'Give users only the minimum access they need to do their job', 'Deny access to all users by default', 'Allow access based on seniority', 2),
(26, 3, 'What is multi-factor authentication (MFA)?', 'Using multiple passwords', 'Using two or more verification methods to confirm identity', 'A very strong single password', 'A firewall with multiple layers', 2),
(27, 3, 'What should you do before clicking a link in an email?', 'Click immediately if it looks important', 'Hover over the link to see the actual URL first', 'Forward it to your friends to check', 'Reply to the email asking if it is safe', 2),
(28, 3, 'What is data encryption?', 'Deleting sensitive data permanently', 'Converting data into a coded format that only authorized parties can read', 'Backing up data to the cloud', 'Compressing files to save storage', 2),
(29, 3, 'What is the purpose of a security audit?', 'To slow down the network', 'To systematically evaluate the security of a system for weaknesses', 'To update antivirus software', 'To install new hardware', 2),
(30, 3, 'What is patch management?', 'Sewing patches on clothing', 'The process of regularly updating software to fix security vulnerabilities', 'Managing network cables', 'Organizing files and folders', 2),
(31, 4, 'What is penetration testing?', 'Testing internet speed with multiple devices', 'Authorized simulated cyberattack to find security weaknesses', 'Testing if passwords can penetrate databases', 'A type of network monitoring', 2),
(32, 4, 'What does OWASP stand for?', 'Online Web Application Security Project', 'Open Web Application Security Project', 'Official Web Access Security Protocol', 'Open Wide Area Security Protocol', 2),
(33, 4, 'What is a honeypot in cybersecurity?', 'A sweet antivirus reward program', 'A decoy system designed to lure and trap attackers', 'A type of encrypted storage', 'A network speed test tool', 2),
(34, 4, 'What is the difference between symmetric and asymmetric encryption?', 'Symmetric is stronger than asymmetric', 'Symmetric uses one key; asymmetric uses a public and private key pair', 'Asymmetric is faster than symmetric', 'There is no difference', 2),
(35, 4, 'What is a CVE number?', 'A Certificate of Virtual Encryption', 'A unique identifier assigned to publicly known cybersecurity vulnerabilities', 'A type of network protocol', 'A classification for antivirus software', 2),
(36, 4, 'What is network segmentation?', 'Dividing a network into smaller isolated sections to limit attack spread', 'Combining multiple networks into one', 'Disconnecting from the internet', 'Speeding up network performance', 1),
(37, 4, 'What is the CIA Triad in cybersecurity?', 'Central Intelligence Agency protocols', 'Confidentiality, Integrity, and Availability — the three core security principles', 'Cyber Incident Analysis framework', 'Computer Identification Algorithm', 2),
(38, 4, 'What is cross-site scripting (XSS)?', 'Writing scripts for multiple websites', 'Injecting malicious scripts into trusted websites to attack users', 'Cross-platform scripting language', 'A CSS animation technique', 2),
(39, 4, 'What is a digital signature?', 'A scanned version of your handwritten signature', 'A cryptographic method to verify authenticity and integrity of a message', 'A type of email signature', 'A logo on a digital certificate', 2),
(40, 4, 'What is the purpose of an Intrusion Detection System (IDS)?', 'Block all incoming internet traffic', 'Monitor network traffic for suspicious activity and known threats', 'Speed up network connections', 'Manage user access permissions', 2),
(41, 5, 'What is a supply chain attack?', 'Attacking delivery trucks carrying hardware', 'Compromising software or hardware in the supply chain before it reaches the end user', 'An attack on e-commerce websites', 'A type of DDoS targeting shopping sites', 2),
(42, 5, 'What is fileless malware?', 'Malware stored in hidden files', 'Malware that operates entirely in memory without writing files to disk', 'A virus that deletes all files', 'Malware stored in cloud services', 2),
(43, 5, 'What does \"defense in depth\" mean?', 'Having a very strong single security layer', 'Using multiple overlapping layers of security controls', 'Defending your network from physical attacks', 'Deep scanning of network traffic only', 2),
(44, 5, 'What is a side-channel attack?', 'Attacking from the side of a building', 'Exploiting information leaked from physical implementation like power or timing', 'An attack through a secondary network port', 'A social engineering technique', 2),
(45, 5, 'What is threat intelligence?', 'The IQ level of a hacker', 'Evidence-based knowledge about existing or emerging threats used to make security decisions', 'A type of antivirus engine', 'Monitoring employee computer activity', 2),
(46, 5, 'What is the purpose of a Security Operations Center (SOC)?', 'A room with secure server storage', 'A team that continuously monitors and responds to cybersecurity incidents', 'A software for encrypting data', 'A backup data center', 2),
(47, 5, 'What is privilege escalation?', 'Promoting an employee to admin role', 'An attack where a user gains higher access rights than they are authorized for', 'Increasing server performance privileges', 'A type of password upgrade process', 2),
(48, 5, 'What is OSINT?', 'A type of cybersecurity firewall', 'Open Source Intelligence — gathering information from publicly available sources', 'Operating System Intelligence Tool', 'Offensive Security Network Intrusion Test', 2),
(49, 5, 'What is a watering hole attack?', 'An attack that poisons water infrastructure', 'Compromising a website that a target group frequently visits', 'A DDoS attack on download servers', 'Flooding a network with data packets', 2),
(50, 5, 'What is the MITRE ATT&CK framework?', 'A physical security framework for buildings', 'A knowledge base of real-world adversary tactics and techniques based on observations', 'A programming language for security tools', 'A type of network monitoring protocol', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `streak` int(11) DEFAULT 0,
  `xp` int(11) DEFAULT 0,
  `last_login` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `streak`, `xp`, `last_login`, `created_at`) VALUES
(1, 'java', 'freefirejava1234@gmail.com', '$2y$10$Ofww5MdhxRpqkaATavuFgeE.sP0ADSmOUGQT.mMoQRAdiWGC1mSm6', 1, 50, '2026-04-18', '2026-04-18 16:16:30'),
(2, 'Nadim', 'nadimdayanstar@gmail.com', '$2y$10$yo7Kr8aYhS.rEnTgJeQ.yulgHPeGh7SEK5c6m1d1CN67tI5KPhgDe', 1, 125, '2026-04-24', '2026-04-18 17:39:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
