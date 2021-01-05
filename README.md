# Weight-Tracker-Diary

## Setup:
1. Install [XAMPP](https://www.apachefriends.org/de/index.html)
2. Open the XAMPP Control Panel and start Apache and MySQL (click "Start" next to it)
3. Navigate to the XAMPPs installation folder (C:\xampp) and open the folder: htdocs
4. Clone this Github repository into the htdocs folder
5. For creating and using the Database do the following:
  1. Install [phpMyAdmin](https://www.phpmyadmin.net/) -> during the installation choose as Host "localhost" and as User "root"
  2. Navigate to: [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
  3. Click the "Database" tab at the top
  4. Under "Create database" type in "phplogin" in the text box and select "utf8_general_ci" as the collation
  5. Click create
  6. Now perform the following SQL comands unter the tab "SQL":
  
       ```SQL
       CREATE TABLE `accounts` (
       `id` int(11) NOT NULL AUTO_INCREMENT,
       `username` varchar(50) NOT NULL,
       `password` varchar(255) NOT NULL,
       `email` varchar(100) NOT NULL,
       PRIMARY KEY (`id`)
       ) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8
       ``` 
       
       ```SQL
       CREATE TABLE `diary_entries` (
       `user_id` int(11) NOT NULL,
       `date` date NOT NULL,
       `feeling` int(11) DEFAULT NULL,
       `sleep` int(11) DEFAULT NULL,
       `sleep_time` int(11) DEFAULT NULL,
       `sports` tinyint(1) DEFAULT NULL,
       `sports_kind` int(11) DEFAULT NULL,
       `weight` decimal(10,0) DEFAULT NULL,
       `individual_entry` varchar(500) DEFAULT NULL,
       PRIMARY KEY (`user_id`,`date`),
       KEY `feeling` (`feeling`),
       KEY `sleep` (`sleep`),
       KEY `sports_kind` (`sports_kind`),
       CONSTRAINT `diary_entries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
       CONSTRAINT `diary_entries_ibfk_2` FOREIGN KEY (`feeling`) REFERENCES `rating` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
       CONSTRAINT `diary_entries_ibfk_3` FOREIGN KEY (`sleep`) REFERENCES `rating` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
       CONSTRAINT `diary_entries_ibfk_4` FOREIGN KEY (`sports_kind`) REFERENCES `sports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
       ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
      ``` 
      
      ```SQL
      CREATE TABLE `rating` (
       `id` int(11) NOT NULL,
       `rating` char(10) DEFAULT NULL,
       PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
      ```
      
      ```SQL    	
      CREATE TABLE `sports` (
       `id` int(11) NOT NULL,
       `sports_kind` char(20) DEFAULT NULL,
       PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
      ``` 

    


