<?php

namespace App\Entity;

use App\Repository\ClassesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassesRepository::class)]
class Classes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $section = null;

    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'classes')]
    private Collection $student;

    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'classes')]
    private Collection $course;

    #[ORM\OneToMany(mappedBy: 'class', targetEntity: Grades::class)]
    private Collection $grades;

    public function __construct()
    {
        $this->student = new ArrayCollection();
        $this->course = new ArrayCollection();
        $this->grades = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): static
    {
        $this->section = $section;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }


    /**
     * @return Collection<int, student>
     */
    public function getStudent(): Collection
    {
        return $this->student;
    }

    public function addStudent(student $student): static
    {
        if (!$this->student->contains($student)) {
            $this->student->add($student);
        }

        return $this;
    }

    public function removeStudent(student $student): static
    {
        $this->student->removeElement($student);

        return $this;
    }

    /**
     * @return Collection<int, course>
     */
    public function getCourse(): Collection
    {
        return $this->course;
    }

    public function addCourse(course $course): static
    {
        if (!$this->course->contains($course)) {
            $this->course->add($course);
        }

        return $this;
    }

    public function removeCourse(course $course): static
    {
        $this->course->removeElement($course);

        return $this;
    }

    /**
     * @return Collection<int, Grades>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grades $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setClass($this);
        }

        return $this;
    }

    public function removeGrade(Grades $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getClass() === $this) {
                $grade->setClass(null);
            }
        }

        return $this;
    }
}
