<?php

	/**
	 * Created by PhpStorm.
	 * User: MrCraftCod
	 * Date: 22/08/2016
	 * Time: 11:23
	 */

	namespace YTT
	{
		class DBHandlerSite
		{
			private $sqlConnection;

			/**
			 * DBHandlerSite constructor.
			 *
			 * @param \PDO $conn
			 */
			public function __construct($conn)
			{
				$this->sqlConnection = $conn;
			}

			/**
			 * @return array
			 */
			public function getUUIDS()
			{
				$query = $this->sqlConnection->query("SELECT DISTINCT `YTTUsers`.`ID`, `YTTUsers`.`UUID` FROM `YTTUsers` INNER JOIN `YTTRecords` ON `YTTUsers`.`UUID`=`YTTRecords`.`UUID` WHERE `TIME` >= DATE_SUB(NOW(), INTERVAL 1 MONTH);");
				if(!$query)
					return array('code' => 500, 'result' => 'err', 'error' => 'E0');
				$uuids = array();
				$i = 0;
				foreach($query as $index => $row)
					$uuids[$i++] = array('ID' => $row['ID'], 'UUID' => $row['UUID']);
				if(count($uuids) > 0)
					return array('code' => 200, 'result' => 'OK', 'uuids' => $uuids);
				return array('code' => 400, 'result' => 'No entry');
			}

			/**
			 * @param string $uuid
			 * @return string|null
			 */
			public function getUsername($uuid)
			{
				$query = $this->sqlConnection->prepare("SELECT `Username` FROM  `YTTUsers` WHERE `UUID`=:uuid;");
				if($query->execute(array(':uuid' => $uuid)))
				{
					if($row = $query->fetch())
						return $row['Username'];
				}
				return null;
			}

			/**
			 * @param string $uuid
			 * @return array
			 */
			public function getTodayStats($uuid)
			{
				$query = $this->sqlConnection->prepare("SELECT * FROM `YTTRecords` WHERE `UUID`=:uuid AND `Time` >= CURDATE();");
				if(!$query->execute(array(':uuid' => $uuid)))
					return array('code' => 500, 'result' => 'err', 'error' => 'E2');
				$stats = array();
				foreach($query->fetchAll() as $index => $row)
					$stats[$row['UID']] = array('uid' => $row['UID'], 'type' => $row['Type'], 'videoid' => $row['VideoID'], 'Stat' => $row['Stat'], 'time' => $row['Time']);
				$username = $this->getUsername($uuid);
				if($username)
				{
					$stats['username'] = $username;
				}
				if(count($stats) > 0)
					return array('code' => 200, 'result' => 'OK', 'stats' => $stats);
				return array('code' => 400, 'result' => 'No entry');
			}

			/**
			 * @param string $uuid
			 * @param int $type
			 * @return int
			 */
			private function getSumRecordType($uuid, $type)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT SUM(`Stat`) AS Total FROM  `YTTRecords` WHERE `Type`=:type AND `UUID`=:uuid;");
				if($query->execute(array(':type' => $type, ':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getTotalWatched($uuid)
			{
				return $this->getSumRecordType($uuid, 1);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getTotalOpened($uuid)
			{
				return $this->getSumRecordType($uuid, 2);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getTotalOpenedCount($uuid)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT COUNT(`Stat`) AS Total FROM  `YTTRecords` WHERE `Type`=2 AND `UUID`=:uuid;");
				if($query->execute(array(':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @param int $type
			 * @return int
			 */
			public function getSumRecordTypeToday($uuid, $type)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT SUM(`Stat`) AS Total FROM  `YTTRecords` WHERE `Type`=:type AND `UUID`=:uuid AND `Time` >= CURDATE();");
				if($query->execute(array(':type' => $type, ':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getTodayWatched($uuid)
			{
				return $this->getSumRecordTypeToday($uuid, 1);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getTodayOpened($uuid)
			{
				return $this->getSumRecordTypeToday($uuid, 2);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getTodayOpenedCount($uuid)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT COUNT(`Stat`) AS Total FROM  `YTTRecords` WHERE `Type`=2 AND `UUID`=:uuid AND `Time` >= CURDATE();");
				if($query->execute(array(':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @param int $type
			 * @param string $start
			 * @param string $end
			 * @return int
			 */
			public function getSumRecordTypePeriod($uuid, $type, $start, $end)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT SUM(`Stat`) AS Total FROM  `YTTRecords` WHERE `Type`=:type AND `UUID`=:uuid AND `Time` >= :start AND `Time` <= :end;");
				if($query->execute(array(':type' => $type, ':uuid' => $uuid, ':start' => $start, ':end' => $end)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @param string $start
			 * @param string $end
			 * @return int
			 */
			public function getPeriodWatched($uuid, $start, $end)
			{
				return $this->getSumRecordTypePeriod($uuid, 1, $start, $end);
			}

			/**
			 * @param string $uuid
			 * @param string $start
			 * @param string $end
			 * @return int
			 */
			public function getPeriodOpened($uuid, $start, $end)
			{
				return $this->getSumRecordTypePeriod($uuid, 2, $start, $end);
			}

			/**
			 * @param string $uuid
			 * @param string $start
			 * @param string $end
			 * @return int
			 */
			public function getPeriodCount($uuid, $start, $end)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT COUNT(`Stat`) AS Total FROM `YTTRecords` WHERE `Type`=2 AND `UUID`=:uuid AND `Time` >= :start AND `Time` < :end;");
				if($query->execute(array('uuid' => $uuid, ':start' => $start, ':end' => $end)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @return array
			 */
			public function getUsersTotalsWatched()
			{
				$result = array();
				foreach($this->sqlConnection->query("SELECT `YTTRecords`.`ID`, `YTTUsers`.`ID` AS `UID`, `YTTRecords`.`Type`, SUM(`YTTRecords`.`Stat`) AS `Stat`, DATE(`YTTRecords`.`Time`) AS `StatDay` FROM `YTTRecords` LEFT JOIN `YTTUsers` ON `YTTRecords`.`UUID` = `YTTUsers`.`UUID` WHERE `YTTRecords`.`Type` = 1 AND DATE(`YTTRecords`.`Time`) >= DATE_SUB(NOW(), INTERVAL 1 MONTH) GROUP BY `YTTRecords`.`UUID`, `StatDay`, `YTTRecords`.`Type`;") as $index => $row)
				{
					$result[$row['ID']] = array('UID' => $row['UID'], 'Stat' => $row['Stat'], 'Date' => $row['StatDay'], 'Type' => $row['Type']);
				}
				return $result;
			}

			/**
			 * @return array
			 */
			public function getUsersTotalsOpened()
			{
				$result = array();
				foreach($this->sqlConnection->query("SELECT `YTTRecords`.`ID`, `YTTUsers`.`ID` AS `UID`, `YTTRecords`.`Type`, SUM(`YTTRecords`.`Stat`) AS `Stat`, DATE(`YTTRecords`.`Time`) AS `StatDay` FROM `YTTRecords` LEFT JOIN `YTTUsers` ON `YTTRecords`.`UUID` = `YTTUsers`.`UUID` WHERE `YTTRecords`.`Type` = 2 AND DATE(`YTTRecords`.`Time`) >= DATE_SUB(NOW(), INTERVAL 1 MONTH) GROUP BY `YTTRecords`.`UUID`, `StatDay`, `YTTRecords`.`Type`;") as $index => $row)
				{
					$result[$row['ID']] = array('UID' => $row['UID'], 'Stat' => $row['Stat'], 'Date' => $row['StatDay'], 'Type' => $row['Type']);
				}
				return $result;
			}

			/**
			 * @return array
			 */
			public function getUsersTotalsCountOpened()
			{
				$result = array();
				foreach($this->sqlConnection->query("SELECT `YTTRecords`.`ID`, `YTTUsers`.`ID` AS `UID`, `YTTRecords`.`Type`, COUNT(`YTTRecords`.`Stat`) AS `Stat`, DATE(`YTTRecords`.`Time`) AS `StatDay` FROM `YTTRecords` LEFT JOIN `YTTUsers` ON `YTTRecords`.`UUID` = `YTTUsers`.`UUID` WHERE `YTTRecords`.`Type` = 2 AND DATE(`YTTRecords`.`Time`) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) GROUP BY `YTTRecords`.`UUID`, `StatDay`, `YTTRecords`.`Type`;") as $index => $row)
				{
					$result[$row['ID']] = array('UID' => $row['UID'], 'Stat' => $row['Stat'], 'Date' => $row['StatDay'], 'Type' => $row['Type']);
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getLast24hOpened($uuid)
			{
				return $this->getSumRecordTypeDays($uuid, 2, 1);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getLast24hWatched($uuid)
			{
				return $this->getSumRecordTypeDays($uuid, 1, 1);
			}

			/**
			 * @param string $uuid
			 * @param int $days
			 * @return int
			 */
			public function getCountDays($uuid, $days)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT COUNT(`Stat`) AS Total FROM `YTTRecords` WHERE `Type`=2 AND `UUID`=:uuid AND `Time` >= DATE_SUB(NOW(), INTERVAL $days DAY);");
				if($query->execute(array(':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @param int $type
			 * @param int $days
			 * @return int
			 */
			private function getSumRecordTypeDays($uuid, $type, $days)
			{
				$result = 0;
				$query = $this->sqlConnection->prepare("SELECT SUM(`Stat`) AS Total FROM  `YTTRecords` WHERE `Type`=:type AND `UUID`=:uuid AND `Time` >= DATE_SUB(NOW(), INTERVAL $days DAY);");
				if($query->execute(array(':type' => $type, ':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Total'];
				}
				return $result;
			}

			/**
			 * @return array
			 */
			public function getUsersTotalsWatchedForever()
			{
				$result = array();
				foreach($this->sqlConnection->query("SELECT `YTTRecords`.`ID`, `YTTUsers`.`ID` AS `UID`, `YTTRecords`.`Type`, SUM(`YTTRecords`.`Stat`) AS `Stat`, DATE(`YTTRecords`.`Time`) AS `StatDay` FROM `YTTRecords` LEFT JOIN `YTTUsers` ON `YTTRecords`.`UUID` = `YTTUsers`.`UUID` WHERE `YTTRecords`.`Type` = 1 GROUP BY `YTTRecords`.`UUID`, `StatDay`, `YTTRecords`.`Type`;") as $index => $row)
				{
					$result[$row['ID']] = array('UID' => $row['UID'], 'Stat' => $row['Stat'], 'Date' => $row['StatDay'], 'Type' => $row['Type']);
				}
				return $result;
			}

			/**
			 * @return array
			 */
			public function getUsersTotalsOpenedForever()
			{
				$result = array();
				foreach($this->sqlConnection->query("SELECT `YTTRecords`.`ID`, `YTTUsers`.`ID` AS `UID`, `YTTRecords`.`Type`, SUM(`YTTRecords`.`Stat`) AS `Stat`, DATE(`YTTRecords`.`Time`) AS `StatDay` FROM `YTTRecords` LEFT JOIN `YTTUsers` ON `YTTRecords`.`UUID` = `YTTUsers`.`UUID` WHERE `YTTRecords`.`Type` = 2 GROUP BY `YTTRecords`.`UUID`, `StatDay`, `YTTRecords`.`Type`;") as $index => $row)
				{
					$result[$row['ID']] = array('UID' => $row['UID'], 'Stat' => $row['Stat'], 'Date' => $row['StatDay'], 'Type' => $row['Type']);
				}
				return $result;
			}

			/**
			 * @return array
			 */
			public function getUsersTotalsCountOpenedForever()
			{
				$result = array();
				foreach($this->sqlConnection->query("SELECT `YTTRecords`.`ID`, `YTTUsers`.`ID` AS `UID`, `YTTRecords`.`Type`, COUNT(`YTTRecords`.`Stat`) AS `Stat`, DATE(`YTTRecords`.`Time`) AS `StatDay` FROM `YTTRecords` LEFT JOIN `YTTUsers` ON `YTTRecords`.`UUID` = `YTTUsers`.`UUID` WHERE `YTTRecords`.`Type` = 2 GROUP BY `YTTRecords`.`UUID`, `StatDay`, `YTTRecords`.`Type`;") as $index => $row)
				{
					$result[$row['ID']] = array('UID' => $row['UID'], 'Stat' => $row['Stat'], 'Date' => $row['StatDay'], 'Type' => $row['Type']);
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getWeekWatched($uuid)
			{
				return $this->getSumRecordTypeDays($uuid, 1, 7);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getWeekOpened($uuid)
			{
				return $this->getSumRecordTypeDays($uuid, 2, 7);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getWeekOpenedCount($uuid)
			{
				return $this->getCountDays($uuid, 7);
			}

			/**
			 * @param string $uuid
			 * @return int
			 */
			public function getLast24hOpenedCount($uuid)
			{
				return $this->getCountDays($uuid, 1);
			}

			/**
			 * @param string $uuid
			 * @return string
			 */
			public function getOldestRecord($uuid)
			{
				$result = "ERROR";
				$query = $this->sqlConnection->prepare('SELECT MIN(`Time`) AS `Oldest` FROM `YTTRecords` WHERE `UUID`=:uuid;');
				if($query->execute(array(':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Oldest'];
				}
				return $result;
			}

			/**
			 * @param string $uuid
			 * @return string
			 */
			public function getMostRecentRecord($uuid)
			{
				$result = "ERROR";
				$query = $this->sqlConnection->prepare('SELECT MAX(`Time`) AS `Oldest` FROM `YTTRecords` WHERE `UUID`=:uuid;');
				if($query->execute(array(':uuid' => $uuid)))
				{
					foreach($query->fetchAll() as $index => $row)
						$result = $row['Oldest'];
				}
				return $result;
			}
		}
	}