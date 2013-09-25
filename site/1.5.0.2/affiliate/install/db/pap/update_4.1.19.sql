ALTER TABLE qu_pap_campaigns CHANGE productid productid LONGTEXT ASCII;

ALTER TABLE qu_pap_transactions ADD couponid CHAR(8) ASCII AFTER channel;

CREATE TABLE qu_pap_coupons (
    couponid CHAR(8) NOT NULL,
    userid CHAR(8),
    bannerid CHAR(8),
    couponcode VARCHAR(100),
    rstatus CHAR(1),
    validfrom DATETIME,
    validto DATETIME,
    maxusecount INTEGER,
    CONSTRAINT PK_qu_pap_coupons PRIMARY KEY (couponid)
);

ALTER TABLE qu_pap_transactions ADD CONSTRAINT qu_pap_coupons_qu_pap_transactions
    FOREIGN KEY (couponid) REFERENCES qu_pap_coupons (couponid);

ALTER TABLE qu_pap_coupons ADD CONSTRAINT qu_pap_users_qu_pap_coupons 
    FOREIGN KEY (userid) REFERENCES qu_pap_users (userid);

ALTER TABLE qu_pap_coupons ADD CONSTRAINT qu_pap_banners_qu_pap_coupons 
    FOREIGN KEY (bannerid) REFERENCES qu_pap_banners (bannerid);