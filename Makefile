CLI=docker compose run --rm wp-cli wp

.PHONY: install reset reset-containers reset-install reset-users reset-comments reset-woocommerce
install: reset-install reset-users reset-woocommerce reset-comments

reset: reset-containers reset-install reset-users reset-woocommerce reset-comments

reset-containers:
	@echo "Preparing containers..."
	@docker compose down -v || true
	@docker compose up -d

reset-install:
	@echo "Waiting 10 seconds for environment to be ready..."
	@sleep 10
	@echo "Installing WordPress..."
	@$(CLI) core install \
		--url="http://localhost" \
		--title="My Awesome WordPress Site" \
		--admin_user=admin \
		--admin_password=admin \
		--admin_email=contact@example.com \
		--skip-email
	@$(CLI) plugin install woocommerce --activate
	@$(CLI) plugin install wordpress-importer --activate

reset-users:
	@echo "Create some sample users..."
	@$(CLI) user create bob bob@wp.local --first_name=Robert --last_name=Carter --nickname=Bob --description="Nice man" --role=author --porcelain
	@$(CLI) user create ann ann@wp.local --first_name=Ann --last_name=Jolly --nickname=Ann --description="Nice woman" --role=author --porcelain
	@$(CLI) user create will will@wp.local --first_name=William --last_name=Arin --nickname=Will --description="Very nice man" --role=author --porcelain

reset-comments:
	@echo "Create some sample comments..."
	@$(CLI) comment create --comment_post_ID=1 --comment_content="hello blog" --comment_author="Mr Robinson" --comment_author_email="robinson@wp.local" --comment_author_IP="201.202.203.204"
	@$(CLI) comment create --comment_post_ID=10 --comment_content="nice website!" --comment_author="Jeff Park" --comment_author_email="jeff@wp.local" --comment_author_IP="201.202.203.205"
	@$(CLI) comment create --comment_post_ID=12 --comment_content="this is really impressive" --comment_author="Linda Johnson" --comment_author_email="linda@wp.local" --comment_author_IP="201.202.203.206"

reset-woocommerce:
	@echo "Importing WooCommerce sample data and creating some more..."
	@$(CLI) import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=create
	@$(CLI) wc customer create --email='justin@woo.local' --user=1 --password='he llo' \
		--billing='{"first_name":"Justin","last_name":"Hills","company":"Google","address_1":"4571 Ersel Street","city":"Dallas","state":"Texas","postcode":"75204","country":"United States","email":"justin@woo.local","phone":"214-927-9108"}' \
		--shipping='{"first_name":"Justin","last_name":"Hills","company":"Google","address_1":"4571 Ersel Street","city":"Dallas","state":"Texas","postcode":"75204","country":"United States","email":"justin@woo.local","phone":"214-927-9108"}'
	@$(CLI) wc customer create --email='otis@woo.local' --user=1 --password='he llo' \
		--billing='{"first_name":"Ottis","last_name":"Bruen","company":"Facebook","address_1":"81 Spring St","city":"New York","state":"North Dakota","postcode":"10012","country":"United States","email":"ottis@woo.local","phone":"(646) 613-1367"}' \
		--shipping='{"first_name":"Ottis","last_name":"Bruen","company":"Facebook","address_1":"81 Spring St","city":"New York","state":"North Dakota","postcode":"10012","country":"United States","email":"ottis@woo.local","phone":"(646) 613-1367"}'
	@$(CLI) wc shop_order create --user=1 --customer_id=6 --line_items='[{"product_id":17},{"product_id":23}]'
	@$(CLI) wc shop_order create --user=1 --customer_id=7 --line_items='[{"product_id":24}]'
	@$(CLI) wc shop_order create --user=1 --customer_id=0 --line_items='[{"product_id":20},{"product_id":22}]' \
		--billing='{"first_name":"Trudie","last_name":"Metz","company":"Amazon","address_1":"135 Wyandot Ave","city":"Marion","state":"Ohio","postcode":"43302","country":"United States","email":"trudie@woo.local","phone":"(740) 383-4031"}' \
		--shipping='{"first_name":"Trudie","last_name":"Metz","company":"Amazon","address_1":"135 Wyandot Ave","city":"Marion","state":"Ohio","postcode":"43302","country":"United States","email":"trudie@woo.local","phone":"(740) 383-4031"}'

.PHONY: test
test:
	@./vendor/bin/phpunit
	@./vendor/bin/ecs check

.PHONY: fix
fix:
	@./vendor/bin/ecs check --fix
