SUBDIRS =  htdocs utils dbutil

EXTRA_DIST = UNINSTALL

uninstall-local:
	rm -rf @RTLOGSDIR@

clean-sample-data: ./dbutil/tabfill.sql
	./dbutil/dbexec < ./dbutil/tabdrop.sql
	./dbutil/dbexec < ./dbutil/tabcreate.sql
	./dbutil/dbexec < ./dbutil/tabfill.sql
	@echo "****************************************************"
	@echo "** The sample data has been successfully cleaned. **"
	@echo "****************************************************"

create-tables: ./dbutil/tabfill.sql
	./dbutil/dbexec < ./dbutil/tabcreate.sql
	./dbutil/dbexec < ./dbutil/tabfill.sql

drop-tables:
	./dbutil/dbexec < ./dbutil/tabdrop.sql

CLEANFILES = *~ $(noinst_SCRIPTS)
