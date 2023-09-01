<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $hasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadTags();
        $this->loadSchoolYears();
        $this->loadProjects();
        $this->loadStudents();
    }

    public function loadTags(): void
    {
        // données statiques
        $datas = [
            [
                'name' => 'HTML',
                'description' => null,
            ],
            [
                'name' => 'CSS',
                'description' => null,
            ],
            [
                'name' => 'JS',
                'description' => null,
            ],
        ];

        foreach ($datas as $data) {
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);

            $this->manager->persist($tag);
        }

        $this->manager->flush();

        // données dynamiques
        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $words = random_int(1, 3);
            $tag->setName($this->faker->unique()->sentence($words));
            $words = random_int(8, 15);
            $tag->setDescription($this->faker->optional($weight = 0.7)->sentence($words));

            $this->manager->persist($tag);
        }

        $this->manager->flush();
    }

    public function loadSchoolYears(): void
    {
        // données statiques
        $datas = [
            [
                'name' => 'Alan Turing',
                'description' => null,
                'startDate' => new DateTime('2022-01-01'),
                'endDate' => new DateTime('2022-12-31'),
            ],
            [
                'name' => 'John von Neuman',
                'description' => null,
                'startDate' => new DateTime('2022-06-01'),
                'endDate' => new DateTime('2023-05-31'),
            ],
            [
                'name' => 'Brendan Eich',
                'description' => null,
                'startDate' => null,
                'endDate' => null,
            ],
        ];

        foreach ($datas as $data) {
            $schoolYear = new SchoolYear();
            $schoolYear->setName($data['name']);
            $schoolYear->setDescription($data['description']);
            $schoolYear->setStartDate($data['startDate']);
            $schoolYear->setEndDate($data['endDate']);

            $this->manager->persist($schoolYear);
        }

        $this->manager->flush();

        // données dynamiques
        for ($i = 0; $i < 10; $i++) {
            $schoolYear = new SchoolYear();

            $words = random_int(2, 4);
            $schoolYear->setName($this->faker->unique()->sentence($words));

            $words = random_int(8, 15);
            $schoolYear->setDescription($this->faker->optional($weight = 0.7)->sentence($words));

            $startDate = $this->faker->dateTimeBetween('-1 year', '-6 months');
            $schoolYear->setStartDate($startDate);

            $endDate = $this->faker->dateTimeBetween('-6 months', 'now');
            $schoolYear->setEndDate($endDate);

            $this->manager->persist($schoolYear);
        }

        $this->manager->flush();
    }

    public function loadProjects(): void
    {
        // récupération de la liste complète des tags
        $repository = $this->manager->getRepository(Tag::class);
        $tags = $repository->findAll();

        // récupération d'un tag à partir de son id
        $htmlTag = $repository->find(1);
        $cssTag = $repository->find(2);
        // récupération du troisième élément de la liste complète
        // c-à-d le tag JS
        $jsTag = $tags[2];

        // données statiques
        $datas = [
            [
                'name' => 'Site vitrine',
                'description' => null,
                'clientName' => 'Alice',
                'startDate' => new DateTime('2022-10-01'),
                'checkpointDate' => new DateTime('2022-11-01'),
                'deliveryDate' => new DateTime('2022-12-01'),
                'tags' => [$htmlTag, $cssTag],
            ],
            [
                'name' => 'Wordpress',
                'description' => null,
                'clientName' => 'Bob',
                'startDate' => new DateTime('2022-02-01'),
                'checkpointDate' => new DateTime('2022-03-01'),
                'deliveryDate' => new DateTime('2022-04-01'),
                'tags' => [$cssTag, $jsTag],
            ],
            [
                'name' => 'API Rest',
                'description' => null,
                'clientName' => 'Charlie',
                'startDate' => new DateTime('2022-05-01'),
                'checkpointDate' => new DateTime('2022-06-01'),
                'deliveryDate' => new DateTime('2022-07-01'),
                'tags' => [$jsTag],
            ],
        ];

        foreach ($datas as $data) {
            $project = new Project();
            $project->setName($data['name']);
            $project->setDescription($data['description']);
            $project->setClientName($data['clientName']);
            $project->setStartDate($data['startDate']);
            $project->setCheckpointDate($data['checkpointDate']);
            $project->setDeliveryDate($data['deliveryDate']);

            foreach ($data['tags'] as $tag) {
                $project->addTag($tag);
            }

            $this->manager->persist($project);
        }

        $this->manager->flush();

        // données dynamiques
        for ($i = 0; $i < 30; $i++) {
            $project = new Project();

            $words = random_int(3, 5);
            $project->setName($this->faker->sentence($words));

            $words = random_int(5, 15);
            $project->setDescription($this->faker->optional(0.7)->sentence($words));

            $project->setClientName($this->faker->name());
            $project->setStartDate($this->faker->dateTimeBetween('-12 months', '-10 months'));
            $project->setCheckpointDate($this->faker->dateTimeBetween('-10 months', '-8 months'));
            $project->setDeliveryDate($this->faker->dateTimeBetween('-8 months', '-6 months'));

            // on choisit le nombre de tags au hasard entre 1 et 4
            $tagsCount = random_int(1, 4);
            // on choisit des tags au hasard depuis la liste complète
            $shortList = $this->faker->randomElements($tags, $tagsCount);

            // on passe revue chaque tag de la short list
            foreach ($shortList as $tag) {
                // on associe un tag avec le projet
                $project->addTag($tag);
            }

            $this->manager->persist($project);
        }

        $this->manager->flush();
    }

    public function loadStudents(): void
    {
        $repository = $this->manager->getRepository(SchoolYear::class);
        $schoolYears = $repository->findAll();

        $alanTuring = $repository->find(1);
        $johnVonNeuman = $repository->find(2);
        $brendanEich = $schoolYears[2];

        $repository = $this->manager->getRepository(Project::class);
        $projects = $repository->findAll();

        $siteVitrine = $repository->find(1);
        $wordpress = $repository->find(2);
        $apiRest = $projects[2];

        $repository = $this->manager->getRepository(Tag::class);
        $tags = $repository->findAll();

        $html = $repository->find(1);
        $css = $repository->find(2);
        $js = $tags[2];

        // données statiques
        $datas = [
            [
                'email' => 'foo@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname' => 'Foo',
                'lastname' => 'Example',
                'schoolYear' => $alanTuring,
                'projects' => [$siteVitrine],
                'tags' => [$html, $css],
            ],
            [
                'email' => 'bar@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname' => 'Bar',
                'lastname' => 'Example',
                'schoolYear' => $johnVonNeuman,
                'projects' => [$wordpress],
                'tags' => [$html, $css],
            ],
            [
                'email' => 'baz@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname' => 'Baz',
                'lastname' => 'Example',
                'schoolYear' => $brendanEich,
                'projects' => [$apiRest],
                'tags' => [$js],
            ],
        ];

        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setRoles($data['roles']);

            $this->manager->persist($user);

            $student = new Student();
            $student->setFirstname($data['firstname']);
            $student->setLastname($data['lastname']);
            $student->setSchoolYear($data['schoolYear']);
            $student->setUser($user);

            // récupération du premier projet de la liste du student
            $project = $data['projects'][0];
            $student->addProject($project);

            foreach ($data['tags'] as $tag) {
                $student->addTag($tag);
            }

            $this->manager->persist($student);
        }

        $this->manager->flush();

        // données dynamiques
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->setEmail($this->faker->unique()->safeEmail());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $this->manager->persist($user);

            $student = new Student();
            $student->setFirstname($this->faker->firstName());
            $student->setLastname($this->faker->lastName());

            $schoolYear = $this->faker->randomElement($schoolYears);
            $student->setSchoolYear($schoolYear);

            $project = $this->faker->randomElement($projects);
            $student->addProject($project);

            $tagsCount = random_int(1, 4);
            $shortList = $this->faker->randomElements($tags, $tagsCount);

            foreach ($shortList as $tag) {
                $student->addTag($tag);
            }

            $student->setUser($user);

            $this->manager->persist($student);
        }

        $this->manager->flush();
    }
}
