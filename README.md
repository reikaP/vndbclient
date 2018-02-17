## VNDB PHP Client for Laravel

### Configuration

Add the package to project dependencies

`` composer require shikakunhq/vndbclient:dev-master``

Register the provider in ``config/app.php``

`` Shikakunhq\VNDBClient\VNDBServiceProvider::class ``

Pull configuration from vendor. Then config will goes to `` config/vndb.php``

`` php artisan vendor:publish --provider="Shikakunhq\VNDBClient\VNDBServiceProvider" --tag="config" ``




### Basic Usage

##### Retrieving information

```php
use Shikakunhq\VNDBClient\VNDBRequest;
class HomeController {
  public function index() {
    dd(VNDBRequest::getInfo('Wagamama High Spec'));
  }
}
```

Result

```
array:10 [▼
  "id" => 17823
  "producer_id" => 3312
  "title" => "Wagamama High Spec"
  "producer" => "Madosoft"
  "original" => "ワガママハイスペック"
  "aliases" => "Wagahigh"
  "released" => "2016-02-04"
  "description" => """
    The story revolves around Narumi Kouki, a high school student who also draws a manga serialized in a weekly magazine. Because the manga he draws is a risqué romantic comedy, he keeps this fact a secret from everyone around him, with his younger sister Toa and his sister's best friend Mihiro being the only ones who know.\n ◀
    \n
    But one day, as the student council president Rokuonji Kaoruko is searching for male members for the student council, she finds out that Kouki is the manga's author. Kouki joins the student council in exchange for Kaoruko not revealing his secret. However, the vice president, Sakuragi R. Ashe, strongly opposes him joining, and in the midst of all this, various requests and troubles of students begin to pile up. \n ◀
    \n
    [From [url=http://www.animenewsnetwork.com/news/2015-08-14/wagamama-high-spec-adult-visual-novel-gets-tv-anime/.91657]Anime News Network[/url]]
    """
  "image" => "https://s.vndb.org/cv/96/33096.jpg"
  "image_nsfw" => false
]

```

##### Custom Commands

```php
use Shikakunhq\VNDBClient\VNDBRequest;
class HomeController {
  public function index() {
    dd(VNDBRequest::command('get vn basic,details (title="Wagamama High Spec")');
  }
}
```

Visit [API Documentation](https://vndb.org/d11) for more usage



