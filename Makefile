# Makefile for DeskDesk development

# Create a relative symlink in the parent directory so Nextcloud can find the
# app by its ID (deskdesk) even when the repo is cloned under a different name.
# Nextcloud requires the directory name to match the <id> in appinfo/info.xml.
dev-link:
	@if [ -L ../deskdesk ] || [ -d ../deskdesk ]; then \
		echo "../deskdesk already present (symlink or directory). Nothing to do."; \
	else \
		ln -s "$$(basename $$(pwd))" ../deskdesk && \
		echo "Created symlink: apps-extra/deskdesk -> $$(basename $$(pwd))"; \
	fi

dev-unlink:
	@if [ -L ../deskdesk ]; then \
		rm ../deskdesk && echo "Removed symlink ../deskdesk"; \
	else \
		echo "No symlink found at ../deskdesk."; \
	fi

.PHONY: dev-link dev-unlink
