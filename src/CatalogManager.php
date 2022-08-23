<?php

namespace PressbooksNetworkCatalog;

use Pressbooks\Container;

class CatalogManager
{
	public function handle()
	{
		return Container::get('Blade')->render(
			'PressbooksNetworkCatalog::catalog', [
				'books' => $this->queryBooks(),
				'filters' => [
					'Subject',
					'License',
					'Last Updated',
					'Institution',
					'Publisher',
					'H5P Activities',
				],
			]
		);
	}

	/**
	 * @return object[]
	 */
	protected function queryBooks(): array
	{
		// TODO: query the books here using the filters provided through the request

		return [
			(object) [
				'authors' => 'John Doe',
				'cover' => 'https://pressbooks.test/app/plugins/pressbooks/assets/dist/images/default-book-cover.jpg',
				'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce et volutpat mi, sed tristique urna. Duis sapien sapien, posuere a dolor ut, iaculis accumsan sapien. Aenean varius purus justo, in lobortis enim malesuada eu. Aenean felis turpis, ullamcorper ac consequat at, sagittis eu sem. Nunc vulputate odio in porttitor maximus. Vivamus blandit pharetra cursus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Maecenas vel ipsum dictum, volutpat lacus nec, tempus justo. Etiam sollicitudin sem enim, in elementum est commodo eu. Donec ut ipsum mauris.',
				'editors' => 'Jane Doe',
				'h5p_count' => 2,
				'institutions' => null,
				'language' => 'English',
				'license' => 'CC BY',
				'publisher' => null,
				'subjects' => 'Communication studies',
				'title' => 'Some awesome title',
				'updated_at' => '08-23-2022',
			],
			(object) [
				'authors' => 'John Doe',
				'cover' => 'https://pressbooks.test/app/plugins/pressbooks/assets/dist/images/default-book-cover.jpg',
				'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce et volutpat mi, sed tristique urna. Duis sapien sapien, posuere a dolor ut, iaculis accumsan sapien. Aenean varius purus justo, in lobortis enim malesuada eu. Aenean felis turpis, ullamcorper ac consequat at, sagittis eu sem. Nunc vulputate odio in porttitor maximus. Vivamus blandit pharetra cursus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Maecenas vel ipsum dictum, volutpat lacus nec, tempus justo. Etiam sollicitudin sem enim, in elementum est commodo eu. Donec ut ipsum mauris.',
				'editors' => 'Jane Doe',
				'h5p_count' => 2,
				'institutions' => null,
				'language' => 'French',
				'license' => 'Public Domain',
				'publisher' => null,
				'subjects' => 'Études de communication',
				'title' => 'Encore un super titre',
				'updated_at' => '08-22-2022',
			],
		];
	}
}
