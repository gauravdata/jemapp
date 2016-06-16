DBTODUMP=notomatoes_shop

SQL="SET group_concat_max_len = 10240;";
SQL="${SQL} SELECT GROUP_CONCAT(table_name separator ' ') ";
SQL="${SQL} FROM information_schema.tables WHERE table_schema='${DBTODUMP}'";
SQL="${SQL} AND (table_name LIKE 'catalog_category_entity%' ";
SQL="${SQL} OR table_name LIKE 'catalog_category_product%' ";
SQL="${SQL} OR table_name LIKE 'catalog_product_entity%' ";
SQL="${SQL} OR table_name LIKE 'catalog_product_link%' ";
SQL="${SQL} OR table_name LIKE 'catalog_product_super%' ";
SQL="${SQL} OR table_name LIKE 'catalog_product_relation%' ";
SQL="${SQL} OR table_name LIKE 'catalog_product_website%' ";
SQL="${SQL} OR table_name LIKE 'catalog_eav_attribute' ";
SQL="${SQL} OR table_name LIKE 'catalog_product_index%' ";
SQL="${SQL} OR table_name LIKE 'cataloginventory_stock%' ";
SQL="${SQL} OR table_name LIKE 'eav_attribute%' ";
SQL="${SQL} OR table_name LIKE 'eav_entity%')";

TBLIST=`mysql -p -AN -e"${SQL}"`
mysqldump -p ${DBTODUMP} ${TBLIST} > "${DBTODUMP}"_catalog_tables.sql
