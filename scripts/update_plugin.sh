#!/bin/bash

set -e

# Require and update magento2 extension
su -c "composer require metrilo/analytics-magento2-extension:master@dev --no-update" magento
su -c "composer update" magento

# Update the modules
su -c "bin/magento setup:upgrade" magento
# Flush the cache
su -c "bin/magento cache:flush" magento
# Deploy static content and reindex
su -c "bin/magento setup:static-content:deploy" magento
su -c "bin/magento indexer:reindex" magento