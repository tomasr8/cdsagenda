USE cdsagenda;

INSERT INTO LEVEL(fid, `level`, title, visibility, categorder)
VALUES (0, 1, 'Main category', 999, 0);

INSERT INTO `AGENDA` (`title`, `id`, `stdate`, `endate`, `location`, `chairman`, `cem`, `status`, `an`, `cd`, `md`, `stylesheet`, `format`, `confidentiality`, `apassword`, `repno`, `fid`, `acomments`, `keywords`, `visibility`, `bld`, `floor`, `room`, `nbsession`, `stime`, `etime`) VALUES
('Test event',	'a241',	'2024-09-10',	'2024-09-10',	'CERN',	'',	'noreply@cern.ch',	'open',	'04842700',	'2024-09-10',	'2024-09-10',	'event',	'timetable',	'open',	'',	'',	'',	'',	'',	'999',	'0',	'',	'0--',	0,	'08:00:00',	'18:00:00');

INSERT INTO COUNTER VALUES ('noAgenda2024', 2);