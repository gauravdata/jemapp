CREATE TABLE qu_g_installedtemplates (
    templateid VARCHAR(32) NOT NULL,
    name TEXT NOT NULL,
    version VARCHAR(40) NOT NULL,
    changed DATETIME NOT NULL,
    contenthash VARCHAR(32) NOT NULL,
    overwritte_existing CHAR(1) NOT NULL DEFAULT 'N',
    CONSTRAINT PK_qu_g_installedtemplates PRIMARY KEY (templateid)
);
