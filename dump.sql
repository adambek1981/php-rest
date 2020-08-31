CREATE TABLE `app`.`products` (
  `id` integer unsigned not null auto_increment,
  `name` varchar(255) not null default '',
  `price` integer unsigned not null default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `app`.`orders` (
  `id` integer unsigned not null auto_increment,
  `number` varchar(255) not null default '',
  `status` enum('new', 'payed') not null default 'new',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `app`.`bindings` (
  `product_id` integer unsigned not null default '0',
  `order_id` integer unsigned not null default '0',
  `count` integer unsigned not null default '1',
  PRIMARY KEY (`product_id`, `order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
