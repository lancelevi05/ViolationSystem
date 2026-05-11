CREATE TABLE `shs_tbl` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `usn` int(33) NOT NULL,
  `lname` varchar(33) NOT NULL,
  `fname` varchar(33) NOT NULL,
  `mname` varchar(32) NOT NULL,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL,
  `genid` int(11) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(333) NOT NULL,
  `iddepartment` int(11) DEFAULT NULL,
  `vio_record` int(11) NOT NULL,
    CONSTRAINT fk_idcourse FOREIGN KEY (idstrandcourse) REFERENCES strandcourse_tbl(idstrandcourse) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_genid FOREIGN KEY (genid) REFERENCES gender_tbl(genid) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_iddepartment FOREIGN KEY (iddepartment) REFERENCES shsdep_tbl(iddepartment) ON UPDATE CASCADE ON DELETE CASCADE
    
);

CREATE TABLE `college_tbl` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `usn` int(33) NOT NULL,
  `lname` varchar(33) NOT NULL,
  `fname` varchar(33) NOT NULL,
  `mname` varchar(32) NOT NULL,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL,
  `genid` int(11) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(333) NOT NULL,
  `iddepartment` int(11) DEFAULT NULL,
  `vio_record` int(11) NOT NULL,
    CONSTRAINT fk_idcourse FOREIGN KEY (idstrandcourse) REFERENCES strandcourse_tbl(idstrandcourse) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_genid FOREIGN KEY (genid) REFERENCES gender_tbl(genid) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_iddepartment FOREIGN KEY (iddepartment) REFERENCES collegedep_tbl(iddepartment) ON UPDATE CASCADE ON DELETE CASCADE
    
);
CREATE TABLE advisory_tbl(
  id INT PRIMARY KEY AUTO_INCREMENT,
  adviser_id INT,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL,
  CONSTRAINT fk_idstrandourse FOREIGN KEY (idstrandcourse) REFERENCES strandcourse_tbl(idstrandcourse) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_adviser_id FOREIGN KEY (adviser_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
    
)

CREATE TABLE `violation` (
  `vi_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `person` varchar(33) NOT NULL,
  `location` varchar(33) NOT NULL,
  `typeviolation` varchar(33) NOT NULL,
  `description` varchar(33) NOT NULL,
  `evidence` varchar(333) NOT NULL,
  `reportedBy` varchar(33) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `status_id` int(11) DEFAULT NULL,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL,
   CONSTRAINT `fk_idcoursevio` FOREIGN KEY (`idstrandcourse`) REFERENCES `strandcourse_tbl` (`idstrandcourse`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `strandcourse_tbl`(`idstrandcourse`, `strandcourse`, `max_section`, `shs_college`) VALUES 
(1, 'Programming', 2,1),
(2, 'Animation', 1,1),
(3, 'Gas', 7,1),
(4, 'HE', 5,1),
(5, 'STEM', 8,1),
(6, 'HUMMS', 6,1),

(7, 'BSIT', 4,0),
(8, 'BSCS', 1,0),
(9, 'BSBA', 5,0),
(10, 'BSHM', 5,0),
(11, 'ACT', 4,0),
(12, 'WAD', 4,0),
(13, 'OAT', 4,0),
(14, 'HRT', 4,0);