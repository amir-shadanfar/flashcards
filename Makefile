up:
	./vendor/bin/sail up

up-daemon:
	./vendor/bin/sail up -d

down:
	./vendor/bin/sail down

shell:
	docker exec -it flashcards_laravel.test_1 /bin/sh

run:
	./vendor/bin/sail artisan flashcard:interactive

test:
	./vendor/bin/sail artisan test

dps:
	docker ps
