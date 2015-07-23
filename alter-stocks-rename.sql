ALTER TABLE stocks.stocks CHANGE ID id int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE stocks.stocks CHANGE LAST last float ;
ALTER TABLE stocks.stocks CHANGE PREVCLOSE prevclose float ;
ALTER TABLE stocks.stocks CHANGE TOTTRDQTY tottrdqty int(11) ;
ALTER TABLE stocks.stocks CHANGE GAIN gain float ;
ALTER TABLE stocks.stocks CHANGE TIMESTAMP timestamp date ;
ALTER TABLE stocks.stocks CHANGE SCRIP_ID scrip_id int(11) ;
ALTER TABLE stocks.stocks CHANGE CREATED_TIME created_time datetime ;
