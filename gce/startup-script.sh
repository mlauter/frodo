#!/bin/bash

# Copyright 2015 Google Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

# [START all]
set -e
export HOME=/root

# [START php]
# Install PHP and dependencies from apt
apt-get update
apt-get install -y git php7.0 php7.0-xml php-mbstring apache2 apache2-doc apache2-utils libapache2-mod-php7.0 sqlite3

# Install Composer
curl -sS https://getcomposer.org/installer | \
    /usr/bin/php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

# Fetch the project ID from the Metadata server
PROJECTID=$(curl -s "http://metadata.google.internal/computeMetadata/v1/project/project-id" -H "Metadata-Flavor: Google")
REPO_NAME=frodo

# Get the application source code
rm -r /var/www/*
git config --global credential.helper gcloud.sh
git clone https://source.developers.google.com/p/$PROJECTID/r/$REPO_NAME /var/www/frodo -b master

# Run Composer
composer install -d /var/www/frodo --no-ansi --no-progress
# [END php]

# [START project_config]
# Fetch the application config file from the Metadata server and add it to the project
curl -s "http://metadata.google.internal/computeMetadata/v1/instance/attributes/project-config" \
  -H "Metadata-Flavor: Google" >> /var/www/frodo/config/settings.yml
# [END project_config]

# [START apache]
sudo a2dismod mpm_event
sudo a2enmod mpm_prefork
sudo a2dissite 000-default.conf

# Enable our apache configuration
cp /var/www/frodo/gce/apache/frodo.com.conf /etc/apache2/sites-available/
sudo a2ensite frodo.com.conf

systemctl restart apache2
# [END apache]
