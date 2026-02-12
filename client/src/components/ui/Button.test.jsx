import { render, screen } from '@testing-library/react'
import { Button } from '../ui/Button'

describe('Button', () => {
  it('renders children correctly', () => {
    render(<Button>Test Button</Button>)
    expect(screen.getByText('Test Button')).toBeInTheDocument()
  })

  it('applies primary variant by default', () => {
    render(<Button>Test</Button>)
    const button = screen.getByRole('button')
    expect(button).toHaveClass('bg-emerald-500')
  })

  it('shows loading spinner when isLoading is true', () => {
    render(<Button isLoading>Loading</Button>)
    expect(screen.getByTestId('loader')).toBeInTheDocument()
  })
})