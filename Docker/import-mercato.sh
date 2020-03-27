#!/bin/bash

# Exit when any command fails
set -e

SRC_DIR=/app/data
DST_DIR=/app/mercato

mkdir -p $DST_DIR

# Unzip datafiles from Mercato stored in makkelijkemarkt objectstore
pushd $SRC_DIR
unzip -o Bestanden.zip $DST_DIR
unzip -o Pasfotos.zip $DST_DIR/fotos
popd

# Import the CSV data into the database
pushd $DST_DIR
php /app/app/console makkelijkemarkt:import:perfectview:vervanger Vervangers.CSV --env=prod
php /app/app/console makkelijkemarkt:import:perfectview:markt Marktnaam.CSV --env=prod
php /app/app/console makkelijkemarkt:import:perfectview:koopman Koopman.CSV --env=prod
php /app/app/console makkelijkemarkt:import:perfectview:sollicitatie Koopman_Markt.CSV --env=prod
php /app/app/console makkelijkemarkt:import:perfectview:foto Koopman.CSV fotos --env=prod
popd