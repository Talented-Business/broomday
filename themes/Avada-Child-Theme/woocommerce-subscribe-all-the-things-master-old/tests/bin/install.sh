#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}
WC_BRANCH=${6-master}
WCS_VERSION=${7-master}

TESTS_LIB_DIR="${WP_TESTS_DIR-/tmp/wordpress-tests-lib}"
TESTS_CORE_DIR="${WP_CORE_DIR-/tmp/wordpress}"

TESTS_DIR="$TESTS_CORE_DIR/.."
INITIAL_DIR=$PWD

echo $TESTS_LIB_DIR
echo $TESTS_CORE_DIR

download() {
	if [ `which curl` ]; then
		curl -s "$1" > "$2";
	elif [ `which wget` ]; then
		wget -nv -O "$2" "$1"
	fi
}

if [[ $WP_VERSION =~ [0-9]+\.[0-9]+(\.[0-9]+)? ]]; then
	WP_TESTS_TAG="tags/$WP_VERSION"
else
	# http serves a single offer, whereas https serves multiple. we only want one
	download http://api.wordpress.org/core/version-check/1.7/ ~/wp-latest.json
	grep '[0-9]+\.[0-9]+(\.[0-9]+)?' ~/wp-latest.json
	LATEST_VERSION=$(grep -o '"version":"[^"]*' ~/wp-latest.json | sed 's/"version":"//')
	if [[ -z "$LATEST_VERSION" ]]; then
		echo "Latest WordPress version could not be found"
		exit 1
	fi
	WP_TESTS_TAG="tags/$LATEST_VERSION"

	rm ~/wp-latest.json
fi

set -ex

install_wp() {

	if [ -d $TESTS_CORE_DIR ]; then
		return;
	fi

	mkdir -p $TESTS_CORE_DIR

	if [ $WP_VERSION == 'latest' ]; then
		local ARCHIVE_NAME='latest'
	else
		local ARCHIVE_NAME="wordpress-$WP_VERSION"
	fi

	download https://wordpress.org/${ARCHIVE_NAME}.tar.gz  ~/wordpress.tar.gz
	tar --strip-components=1 -zxmf ~/wordpress.tar.gz -C $TESTS_CORE_DIR

	download https://raw.github.com/markoheijnen/wp-mysqli/master/db.php $TESTS_CORE_DIR/wp-content/db.php
}

install_test_suite() {
	# portable in-place argument for both GNU sed and Mac OSX sed
	if [[ $(uname -s) == 'Darwin' ]]; then
		local ioption='-i .bak'
	else
		local ioption='-i'
	fi

	# set up testing suite if it doesn't yet exist
	if [ ! -d $TESTS_LIB_DIR ]; then
		# set up testing suite
		mkdir -p $TESTS_LIB_DIR
		svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/ $TESTS_LIB_DIR/includes
		svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/ $TESTS_LIB_DIR/data
	fi

	cd $TESTS_LIB_DIR

	if [ ! -f wp-tests-config.php ]; then
		download https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php "$TESTS_LIB_DIR"/wp-tests-config.php
		sed $ioption "s:dirname( __FILE__ ) . '/src/':'$TESTS_CORE_DIR/':" "$TESTS_LIB_DIR"/wp-tests-config.php
		sed $ioption "s/youremptytestdbnamehere/$DB_NAME/" "$TESTS_LIB_DIR"/wp-tests-config.php
		sed $ioption "s/yourusernamehere/$DB_USER/" "$TESTS_LIB_DIR"/wp-tests-config.php
		sed $ioption "s/yourpasswordhere/$DB_PASS/" "$TESTS_LIB_DIR"/wp-tests-config.php
		sed $ioption "s|localhost|${DB_HOST}|" "$TESTS_LIB_DIR"/wp-tests-config.php
	fi

}

install_db() {
	# parse DB_HOST for port or socket references
	local PARTS=(${DB_HOST//\:/ })
	local DB_HOSTNAME=${PARTS[0]};
	local DB_SOCK_OR_PORT=${PARTS[1]};
	local EXTRA=""

	if ! [ -z $DB_HOSTNAME ] ; then
		if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
			EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
		elif ! [ -z $DB_SOCK_OR_PORT ] ; then
			EXTRA=" --socket=$DB_SOCK_OR_PORT"
		elif ! [ -z $DB_HOSTNAME ] ; then
			EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
		fi
	fi

	# create database
	mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA || true
}

install_wc() {

    cd $INITIAL_DIR

	if [ ! -d ../woocommerce ]; then
		git clone https://github.com/woocommerce/woocommerce ../woocommerce
	fi

	cd ../woocommerce

	git checkout $WC_BRANCH
	git pull

}

install_wcs() {

	cd $INITIAL_DIR

	if [ ! -d ../woocommerce-subscriptions ]; then
        git clone https://$GITHUB_TOKEN@github.com/Prospress/woocommerce-subscriptions.git ../woocommerce-subscriptions -b $WCS_VERSION
    fi
}


install_wp
install_test_suite
install_db
install_wc
install_wcs
