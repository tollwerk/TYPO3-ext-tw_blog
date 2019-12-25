CREATE TABLE pages
(
    tx_twblog_blog_teaser_image     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    tx_twblog_blog_teaser_text      TEXT             NOT NULL DEFAULT '',
    tx_twblog_blog_related_posts VARCHAR(32)               DEFAULT '' NOT NULL,
    tx_twblog_blog_authors          INT(11) UNSIGNED NOT NULL DEFAULT '0',
    tx_twblog_blog_series           INT(11) UNSIGNED NOT NULL DEFAULT '0',
    tx_twblog_blog_comments         VARCHAR(255)              DEFAULT '' NOT NULL
);

CREATE TABLE be_users
(
    tx_twblog_frontend_name  VARCHAR(255)              DEFAULT '' NOT NULL,
    tx_twblog_frontend_image INT(11) UNSIGNED NOT NULL DEFAULT '0'
);

CREATE TABLE tx_twblog_blog_post_author_mm
(
    uid_local   INT(11) UNSIGNED DEFAULT '0' NOT NULL,
    uid_foreign INT(11) UNSIGNED DEFAULT '0' NOT NULL,
    sorting     INT(11) UNSIGNED DEFAULT '0' NOT NULL,
    tablenames  VARCHAR(255)     DEFAULT ''  NOT NULL,

    PRIMARY KEY (uid_local, uid_foreign),
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_twblog_domain_model_comment
(
    uid          INT(11)                         NOT NULL AUTO_INCREMENT,
    pid          INT(11)             DEFAULT '0' NOT NULL,
    tstamp       INT(11) UNSIGNED    DEFAULT '0' NOT NULL,
    crdate       INT(11) UNSIGNED    DEFAULT '0' NOT NULL,
    cruser_id    INT(11) UNSIGNED    DEFAULT '0' NOT NULL,
    deleted      TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    hidden       TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,

    name         VARCHAR(255)        DEFAULT ''  NOT NULL,
    email        VARCHAR(255)        DEFAULT ''  NOT NULL,
    url          VARCHAR(255)        DEFAULT ''  NOT NULL,
    parent       INT(11)             DEFAULT '0' NOT NULL,
    parent_table VARCHAR(255)        DEFAULT ''  NOT NULL,
    text         TEXT                DEFAULT ''  NOT NULL,
    replies      VARCHAR(255)        DEFAULT ''  NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_twblog_domain_model_blogseries
(
    uid       INT(11)                         NOT NULL AUTO_INCREMENT,
    pid       INT(11)             DEFAULT '0' NOT NULL,
    tstamp    INT(11) UNSIGNED    DEFAULT '0' NOT NULL,
    crdate    INT(11) UNSIGNED    DEFAULT '0' NOT NULL,
    cruser_id INT(11) UNSIGNED    DEFAULT '0' NOT NULL,
    deleted   TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    hidden    TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,

    title     VARCHAR(255)        DEFAULT ''  NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);
